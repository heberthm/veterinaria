<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaApertura;
use App\Models\CajaMovimiento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CajaController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth');
        
        // 🔥 SOLO ADMINISTRADORES PUEDEN GESTIONAR CAJA
        $this->middleware('permission:gestionar_caja')->except(['index', 'datatable']);
        $this->middleware('permission:ver_caja')->only(['index', 'datatable']);
    }
    public function index()
    {
        // Obtener la caja principal (o crear una por defecto)
        $caja = Caja::where('tenant_id', auth()->user()->tenant_id ?? null)
            ->where('activa', true)
            ->first();

        if (!$caja) {
            $caja = Caja::create([
                'tenant_id' => auth()->user()->tenant_id ?? null,
                'nombre' => 'Caja Principal',
                'descripcion' => 'Caja principal de la clínica',
                'saldo_inicial' => 0,
                'saldo_actual' => 0,
                'estado' => 'cerrada',
                'activa' => true,
            ]);
        }

        $aperturaActual = $caja->aperturaActual();

        return view('caja', compact('caja', 'aperturaActual'));
    }

    public function datatable(Request $request)
    {
        $movimientos = CajaMovimiento::with(['usuario', 'venta', 'factura'])
            ->whereHas('caja', function($q) {
                $q->where('tenant_id', auth()->user()->tenant_id ?? null);
            });

        if ($request->has('caja_id') && $request->caja_id) {
            $movimientos->where('caja_id', $request->caja_id);
        }

        return DataTables::of($movimientos)
            ->addColumn('fecha', function($movimiento) {
                return $movimiento->created_at->format('d/m/Y H:i');
            })
            ->addColumn('tipo_label', function($movimiento) {
                $colors = ['ingreso' => 'success', 'egreso' => 'danger'];
                return '<span class="badge badge-' . ($colors[$movimiento->tipo] ?? 'secondary') . '">' 
                    . CajaMovimiento::TIPOS[$movimiento->tipo] . '</span>';
            })
            ->addColumn('categoria_label', function($movimiento) {
                return CajaMovimiento::CATEGORIAS[$movimiento->categoria] ?? $movimiento->categoria;
            })
            ->addColumn('monto_formateado', function($movimiento) {
                $signo = $movimiento->tipo === 'ingreso' ? '+' : '-';
                return '<span class="text-' . ($movimiento->tipo === 'ingreso' ? 'success' : 'danger') . '">'
                    . $signo . '$ ' . number_format($movimiento->monto, 0, ',', '.') . '</span>';
            })
            ->addColumn('usuario_nombre', function($movimiento) {
                return $movimiento->usuario->name ?? 'N/A';
            })
            ->addColumn('acciones', function($movimiento) {
                return '
                    <button class="btn btn-sm btn-primary" onclick="verMovimiento(' . $movimiento->id . ')" title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                ';
            })
            ->rawColumns(['tipo_label', 'monto_formateado', 'acciones'])
            ->make(true);
    }

    public function abrir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caja_id' => 'required|exists:cajas,id',
            'saldo_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $caja = Caja::findOrFail($request->caja_id);

        if ($caja->estaAbierta()) {
            return response()->json([
                'success' => false,
                'message' => 'La caja ya está abierta.'
            ], 422);
        }

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

        $caja->update([
            'estado' => 'abierta',
            'saldo_actual' => $request->saldo_inicial,
            'usuario_apertura_id' => auth()->id(),
            'fecha_apertura' => now(),
        ]);

        CajaMovimiento::create([
            'caja_id' => $caja->id,
            'caja_apertura_id' => $apertura->id,
            'usuario_id' => auth()->id(),
            'tipo' => 'ingreso',
            'categoria' => 'apertura',
            'monto' => $request->saldo_inicial,
            'saldo_anterior' => 0,
            'saldo_nuevo' => $request->saldo_inicial,
            'descripcion' => 'Apertura de caja - Saldo inicial: $' . number_format($request->saldo_inicial, 0, ',', '.'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caja abierta exitosamente',
            'caja' => $caja,
            'apertura' => $apertura
        ]);
    }

    public function cerrar(Request $request)
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

        $caja = Caja::findOrFail($request->caja_id);

        if (!$caja->estaAbierta()) {
            return response()->json([
                'success' => false,
                'message' => 'La caja ya está cerrada.'
            ], 422);
        }

        $apertura = $caja->aperturaActual();

        if (!$apertura) {
            return response()->json([
                'success' => false,
                'message' => 'No hay una apertura activa.'
            ], 422);
        }

        $ingresos = CajaMovimiento::where('caja_apertura_id', $apertura->id)
            ->where('tipo', 'ingreso')
            ->where('categoria', '!=', 'apertura')
            ->sum('monto');

        $egresos = CajaMovimiento::where('caja_apertura_id', $apertura->id)
            ->where('tipo', 'egreso')
            ->sum('monto');

        $saldoFinal = $apertura->saldo_inicial + $ingresos - $egresos;

        $apertura->update([
            'saldo_final' => $saldoFinal,
            'fecha_cierre' => now(),
            'estado' => 'cerrada',
        ]);

        $caja->update([
            'estado' => 'cerrada',
            'saldo_actual' => $saldoFinal,
            'usuario_cierre_id' => auth()->id(),
            'fecha_cierre' => now(),
        ]);

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

        return response()->json([
            'success' => true,
            'message' => 'Caja cerrada exitosamente',
            'caja' => $caja,
            'saldo_final' => $saldoFinal
        ]);
    }

    public function ingreso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caja_id' => 'required|exists:cajas,id',
            'monto' => 'required|numeric|min:0.01',
            'categoria' => 'required|in:venta,factura,abono,ajuste',
            'descripcion' => 'required|string|min:5',
            'metodo_pago' => 'nullable|string',
            'referencia' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $caja = Caja::findOrFail($request->caja_id);

        if (!$caja->estaAbierta()) {
            return response()->json([
                'success' => false,
                'message' => 'La caja está cerrada.'
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
        $saldoNuevo = $saldoAnterior + $request->monto;

        CajaMovimiento::create([
            'caja_id' => $caja->id,
            'caja_apertura_id' => $apertura->id,
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

        return response()->json([
            'success' => true,
            'message' => 'Ingreso registrado exitosamente',
            'saldo_actual' => $saldoNuevo
        ]);
    }

    public function egreso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caja_id' => 'required|exists:cajas,id',
            'monto' => 'required|numeric|min:0.01',
            'categoria' => 'required|in:gasto,retiro,ajuste',
            'descripcion' => 'required|string|min:5',
            'metodo_pago' => 'nullable|string',
            'referencia' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $caja = Caja::findOrFail($request->caja_id);

        if (!$caja->estaAbierta()) {
            return response()->json([
                'success' => false,
                'message' => 'La caja está cerrada.'
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

        CajaMovimiento::create([
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

        $caja->update(['saldo_actual' => $saldoNuevo]);

        return response()->json([
            'success' => true,
            'message' => 'Egreso registrado exitosamente',
            'saldo_actual' => $saldoNuevo
        ]);
    }

    public function resumen(Request $request)
    {
        $cajaId = $request->get('caja_id');

        $caja = Caja::with(['movimientos'])->findOrFail($cajaId);

        $movimientos = $caja->movimientos;

        $resumen = [
            'total_ingresos' => $movimientos->where('tipo', 'ingreso')->sum('monto'),
            'total_egresos' => $movimientos->where('tipo', 'egreso')->sum('monto'),
            'saldo_actual' => $caja->saldo_actual,
            'total_ventas' => $movimientos->where('categoria', 'venta')->sum('monto'),
            'total_facturas' => $movimientos->where('categoria', 'factura')->sum('monto'),
            'total_gastos' => $movimientos->where('categoria', 'gasto')->sum('monto'),
            'total_retiros' => $movimientos->where('categoria', 'retiro')->sum('monto'),
        ];

        return response()->json($resumen);
    }

    public function movimiento($id)
    {
        $movimiento = CajaMovimiento::with(['usuario', 'venta', 'factura'])
            ->findOrFail($id);

        return view('modales.movimiento-detalle', compact('movimiento'));
    }
}