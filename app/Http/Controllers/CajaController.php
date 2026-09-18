<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaApertura;
use App\Models\CajaMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class CajaController extends Controller
{
    /**
     * Constructor - requiere autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar vista de caja
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 🔥 OBTENER O CREAR LA CAJA PRINCIPAL DEL TENANT
        $tenantId = auth()->user()->tenant_id ?? null;

        $caja = Caja::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'activa' => true,
            ],
            [
                'nombre' => 'Caja Principal',
                'descripcion' => 'Caja principal de la clínica',
                'saldo_inicial' => 0,
                'saldo_actual' => 0,
                'estado' => 'cerrada',
            ]
        );

        $aperturaActual = $caja->aperturaActual();

        return view('caja', compact('caja', 'aperturaActual'));
    }

    /**
     * DataTable para movimientos de caja
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable(Request $request)
    {
        $query = CajaMovimiento::with(['usuario', 'venta', 'factura'])
            ->whereHas('caja', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id ?? null);
            });

        // Filtro por caja específica
        if ($request->has('caja_id') && $request->caja_id) {
            $query->where('caja_id', $request->caja_id);
        }

        // Filtro por rango de fechas (opcional)
        if ($request->has('fecha_inicio') && $request->fecha_inicio) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin') && $request->fecha_fin) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Filtro por tipo (ingreso/egreso)
        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        return DataTables::of($query)
            ->addColumn('fecha', function($movimiento) {
                return $movimiento->created_at->format('d/m/Y H:i');
            })
            ->addColumn('tipo_label', function($movimiento) {
                $colors = ['ingreso' => 'success', 'egreso' => 'danger'];
                $icons = ['ingreso' => 'fa-arrow-up', 'egreso' => 'fa-arrow-down'];
                $label = CajaMovimiento::TIPOS[$movimiento->tipo] ?? $movimiento->tipo;
                $color = $colors[$movimiento->tipo] ?? 'secondary';
                $icon = $icons[$movimiento->tipo] ?? 'fa-circle';
                
                return '<span class="badge badge-' . $color . '">' 
                    . '<i class="fas ' . $icon . ' mr-1"></i>' 
                    . $label . '</span>';
            })
            ->addColumn('categoria_label', function($movimiento) {
                $categorias = [
                    'venta' => 'Venta',
                    'factura' => 'Factura',
                    'abono' => 'Abono',
                    'gasto' => 'Gasto',
                    'retiro' => 'Retiro',
                    'ajuste' => 'Ajuste',
                    'apertura' => 'Apertura',
                    'cierre' => 'Cierre',
                ];
                return $categorias[$movimiento->categoria] ?? $movimiento->categoria;
            })
            ->addColumn('monto_formateado', function($movimiento) {
                $signo = $movimiento->tipo === 'ingreso' ? '+' : '-';
                $color = $movimiento->tipo === 'ingreso' ? 'success' : 'danger';
                return '<span class="text-' . $color . ' font-weight-bold">'
                    . $signo . ' $' . number_format($movimiento->monto, 0, ',', '.') . '</span>';
            })
            ->addColumn('saldo_anterior_formateado', function($movimiento) {
                return '$' . number_format($movimiento->saldo_anterior ?? 0, 0, ',', '.');
            })
            ->addColumn('saldo_nuevo_formateado', function($movimiento) {
                return '$' . number_format($movimiento->saldo_nuevo ?? 0, 0, ',', '.');
            })
            ->addColumn('usuario_nombre', function($movimiento) {
                return $movimiento->usuario->name ?? 'N/A';
            })
            ->addColumn('metodo_pago_label', function($movimiento) {
                $metodos = [
                    'efectivo' => 'Efectivo',
                    'tarjeta_credito' => 'Tarjeta Crédito',
                    'tarjeta_debito' => 'Tarjeta Débito',
                    'transferencia' => 'Transferencia',
                    'nequi' => 'Nequi',
                    'daviplata' => 'DaviPlata',
                    'qr' => 'QR',
                ];
                return $metodos[$movimiento->metodo_pago] ?? ($movimiento->metodo_pago ?? '—');
            })
            ->rawColumns(['tipo_label', 'monto_formateado'])
            ->make(true);
    }

    /**
     * Abrir caja
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function abrir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caja_id' => 'required|exists:cajas,id',
            'saldo_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $caja = Caja::findOrFail($request->caja_id);

            // Verificar que la caja no esté ya abierta
            if ($caja->estaAbierta()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La caja ya está abierta.'
                ], 422);
            }

            // Crear registro de apertura
            $apertura = CajaApertura::create([
                'caja_id' => $caja->id,
                'usuario_id' => auth()->id(),
                'saldo_inicial' => $request->saldo_inicial,
                'saldo_final' => null,
                'fecha_apertura' => now(),
                'fecha_cierre' => null,
                'estado' => 'abierta',
                'observaciones' => $request->observaciones,
            ]);

            // Actualizar la caja
            $caja->update([
                'estado' => 'abierta',
                'saldo_actual' => $request->saldo_inicial,
                'usuario_apertura_id' => auth()->id(),
                'fecha_apertura' => now(),
            ]);

            // Registrar movimiento de apertura
            CajaMovimiento::create([
                'caja_id' => $caja->id,
                'caja_apertura_id' => $apertura->id,
                'usuario_id' => auth()->id(),
                'tipo' => 'ingreso',
                'categoria' => 'apertura',
                'monto' => $request->saldo_inicial,
                'saldo_anterior' => 0,
                'saldo_nuevo' => $request->saldo_inicial,
                'metodo_pago' => 'efectivo',
                'descripcion' => 'Apertura de caja - Saldo inicial: $' . number_format($request->saldo_inicial, 0, ',', '.'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Caja abierta exitosamente',
                'caja' => $caja->fresh(),
                'apertura' => $apertura,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al abrir la caja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar caja
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cerrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caja_id' => 'required|exists:cajas,id',
            'monto_cierre' => 'nullable|numeric|min:0',
            'observaciones_cierre' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $caja = Caja::findOrFail($request->caja_id);

            // Verificar que la caja esté abierta
            if (!$caja->estaAbierta()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La caja ya está cerrada.'
                ], 422);
            }

            // Obtener la apertura activa
            $apertura = $caja->aperturaActual();

            if (!$apertura) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una apertura activa.'
                ], 422);
            }

            // Calcular totales
            $ingresos = CajaMovimiento::where('caja_apertura_id', $apertura->id)
                ->where('tipo', 'ingreso')
                ->where('categoria', '!=', 'apertura')
                ->sum('monto');

            $egresos = CajaMovimiento::where('caja_apertura_id', $apertura->id)
                ->where('tipo', 'egreso')
                ->sum('monto');

            $saldoFinal = $apertura->saldo_inicial + $ingresos - $egresos;

            // Actualizar la apertura
            $apertura->update([
                'saldo_final' => $saldoFinal,
                'fecha_cierre' => now(),
                'estado' => 'cerrada',
            ]);

            // Actualizar la caja
            $caja->update([
                'estado' => 'cerrada',
                'saldo_actual' => $saldoFinal,
                'usuario_cierre_id' => auth()->id(),
                'fecha_cierre' => now(),
            ]);

            // Registrar movimiento de cierre
            CajaMovimiento::create([
                'caja_id' => $caja->id,
                'caja_apertura_id' => $apertura->id,
                'usuario_id' => auth()->id(),
                'tipo' => 'egreso',
                'categoria' => 'cierre',
                'monto' => $saldoFinal,
                'saldo_anterior' => $saldoFinal,
                'saldo_nuevo' => 0,
                'descripcion' => 'Cierre de caja - Saldo final: $' . number_format($saldoFinal, 0, ',', '.'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Caja cerrada exitosamente',
                'caja' => $caja->fresh(),
                'saldo_final' => $saldoFinal,
                'ingresos' => $ingresos,
                'egresos' => $egresos,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar la caja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar ingreso
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
  public function ingreso(Request $request)
{
    $validator = Validator::make($request->all(), [
        'caja_id' => 'required|exists:cajas,id',
        'monto' => 'required|numeric|min:0.01',
        'categoria' => 'required|in:venta,factura,abono,ajuste',
        'descripcion' => 'required|string|min:3|max:500',
        'metodo_pago' => 'nullable|string|max:50',
        'referencia' => 'nullable|string|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $caja = Caja::findOrFail($request->caja_id);

        if (!$caja->estaAbierta()) {
            return response()->json([
                'success' => false,
                'message' => 'La caja está cerrada. Debe abrir la caja primero.'
            ], 422);
        }

        // 🔥 CORRECCIÓN: Obtener la apertura correctamente
        $apertura = $caja->aperturaActual();

        // Si el método devuelve una relación, obtenemos el primero
        if ($apertura instanceof \Illuminate\Database\Eloquent\Relations\HasOne) {
            $apertura = $apertura->first();
        }

        if (!$apertura) {
            return response()->json([
                'success' => false,
                'message' => 'No hay una apertura activa.'
            ], 422);
        }

        $saldoAnterior = $caja->saldo_actual;
        $saldoNuevo = $saldoAnterior + $request->monto;

        // Registrar movimiento
        $movimiento = CajaMovimiento::create([
            'caja_id' => $caja->id,
            'caja_apertura_id' => $apertura->id,  // ← Ahora sí funciona
            'usuario_id' => auth()->id(),
            'tipo' => 'ingreso',
            'categoria' => $request->categoria,
            'monto' => $request->monto,
            'saldo_anterior' => $saldoAnterior,
            'saldo_nuevo' => $saldoNuevo,
            'metodo_pago' => $request->metodo_pago,
            'referencia' => $request->referencia,
            'descripcion' => $request->descripcion,
        ]);

        $caja->update(['saldo_actual' => $saldoNuevo]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Ingreso registrado exitosamente',
            'movimiento' => $movimiento,
            'saldo_actual' => $saldoNuevo,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al registrar el ingreso: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Registrar egreso
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function egreso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caja_id' => 'required|exists:cajas,id',
            'monto' => 'required|numeric|min:0.01',
            'categoria' => 'required|in:gasto,retiro,ajuste',
            'descripcion' => 'required|string|min:3|max:500',
            'metodo_pago' => 'nullable|string|max:50',
            'referencia' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $caja = Caja::findOrFail($request->caja_id);

            if (!$caja->estaAbierta()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La caja está cerrada. Debe abrir la caja primero.'
                ], 422);
            }

            if ($request->monto > $caja->saldo_actual) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente. Saldo actual: $' . number_format($caja->saldo_actual, 0, ',', '.')
                ], 422);
            }

            $apertura = $caja->aperturaActual();

            if (!$apertura) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una apertura activa.'
                ], 422);
            }

            $saldoAnterior = $caja->saldo_actual;
            $saldoNuevo = $saldoAnterior - $request->monto;

            // Registrar movimiento
            $movimiento = CajaMovimiento::create([
                'caja_id' => $caja->id,
                'caja_apertura_id' => $apertura->id,
                'usuario_id' => auth()->id(),
                'tipo' => 'egreso',
                'categoria' => $request->categoria,
                'monto' => $request->monto,
                'saldo_anterior' => $saldoAnterior,
                'saldo_nuevo' => $saldoNuevo,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'descripcion' => $request->descripcion,
            ]);

            // Actualizar saldo de la caja
            $caja->update(['saldo_actual' => $saldoNuevo]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Egreso registrado exitosamente',
                'movimiento' => $movimiento,
                'saldo_actual' => $saldoNuevo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el egreso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen de caja
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resumen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caja_id' => 'required|exists:cajas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $caja = Caja::with(['movimientos'])->findOrFail($request->caja_id);
            $movimientos = $caja->movimientos;

            $resumen = [
                'saldo_inicial' => $caja->saldo_inicial,
                'saldo_actual' => $caja->saldo_actual,
                'total_ingresos' => $movimientos->where('tipo', 'ingreso')
                    ->where('categoria', '!=', 'apertura')
                    ->sum('monto'),
                'total_egresos' => $movimientos->where('tipo', 'egreso')
                    ->where('categoria', '!=', 'cierre')
                    ->sum('monto'),
                'total_ventas' => $movimientos->where('categoria', 'venta')->sum('monto'),
                'total_facturas' => $movimientos->where('categoria', 'factura')->sum('monto'),
                'total_gastos' => $movimientos->where('categoria', 'gasto')->sum('monto'),
                'total_retiros' => $movimientos->where('categoria', 'retiro')->sum('monto'),
                'total_abonos' => $movimientos->where('categoria', 'abono')->sum('monto'),
                'cantidad_movimientos' => $movimientos->count(),
            ];

            return response()->json([
                'success' => true,
                'resumen' => $resumen,
                'caja' => $caja,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el resumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el movimiento (detalle)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function movimiento($id)
    {
        try {
            $movimiento = CajaMovimiento::with(['usuario', 'venta', 'factura', 'caja'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'movimiento' => $movimiento,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Movimiento no encontrado'
            ], 404);
        }
    }

    /**
     * Obtener la caja activa actual
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cajaActiva()
    {
        try {
            $caja = Caja::where('tenant_id', auth()->user()->tenant_id ?? null)
                ->where('estado', 'abierta')
                ->where('activa', true)
                ->orderBy('fecha_apertura', 'desc')
                ->first();

            if (!$caja) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja abierta'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'caja' => $caja,
                'apertura' => $caja->aperturaActual(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Historial de cierres
     *
     * @return \Illuminate\Http\Response
     */
    public function historial()
    {
        $aperturas = CajaApertura::with(['caja', 'usuario', 'movimientos'])
            ->whereHas('caja', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id ?? null);
            })
            ->where('estado', 'cerrada')
            ->orderBy('fecha_cierre', 'desc')
            ->paginate(20);

        return view('caja.historial', compact('aperturas'));
    }

    /**
     * Exportar movimientos a PDF
     *
     * @param  int  $aperturaId
     * @return \Illuminate\Http\Response
     */
    public function exportarPdf($aperturaId)
    {
        $apertura = CajaApertura::with(['caja', 'usuario', 'movimientos.usuario'])
            ->findOrFail($aperturaId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('caja.pdf', compact('apertura'))
            ->setPaper('letter');

        $nombreArchivo = 'caja-' . $apertura->id . '-' . $apertura->fecha_apertura->format('Y-m-d') . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}