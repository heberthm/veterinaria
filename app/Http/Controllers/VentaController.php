<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\Mascota;
use App\Models\Producto;
use App\Models\CategoriaProducto;
use App\Models\Caja;
use App\Models\CajaMovimiento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar punto de venta (POS)
     */
    public function pos()
    {
        $tenantId = auth()->user()->tenant_id ?? null;

        // ============================================================
        // CAJA ACTUAL
        // ============================================================
        $cajaActual = Caja::where('tenant_id', $tenantId)
            ->where('estado', 'abierta')
            ->orderBy('fecha_apertura', 'desc')
            ->first();

        if ($cajaActual) {
            $cajaActual->load('usuarioApertura');
        }

        // ============================================================
        // PRODUCTOS
        // ============================================================
        $productos = Producto::with('categoria')
            ->where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('tipo', 'producto')->orWhereNull('tipo');
            })
            ->where(function($q) {
                $q->where('activo', true)->orWhereNull('activo');
            })
            ->get();

        // ============================================================
        // SERVICIOS
        // ============================================================
        $servicios = Producto::with('categoria')
            ->where('tenant_id', $tenantId)
            ->where('tipo', 'servicio')
            ->get();

        // ============================================================
        // PAQUETES
        // ============================================================
        $paquetes = Producto::with('categoria')
            ->where('tenant_id', $tenantId)
            ->where('tipo', 'paquete')
            ->get();

        // ============================================================
        // CATEGORÍAS
        // ============================================================
        $categorias = CategoriaProducto::where('tenant_id', $tenantId)->get();

        // ============================================================
        // CONSECUTIVO
        // ============================================================
        $ultimaVenta = Venta::where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->first();

        $siguienteConsecutivo = $ultimaVenta 
            ? str_pad(intval(substr($ultimaVenta->numero_factura ?? 0, -6)) + 1, 6, '0', STR_PAD_LEFT)
            : '000001';

        return view('ventas-pos', compact(
            'cajaActual',
            'productos',
            'servicios',
            'paquetes',
            'categorias',
            'siguienteConsecutivo'
        ));
    }

    /**
     * Guardar venta desde POS
     */
    public function store(Request $request)
    {
        // ============================================================
        // VALIDACIONES
        // ============================================================
        $validator = Validator::make($request->all(), [
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0',
            'cliente_id' => 'nullable|exists:clientes,id',
            'mascota_id' => 'nullable|exists:mascotas,id',
            'metodo_pago' => 'nullable|string|max:50',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:500',

            // 🔥 CAMPOS DE PAGO
            'referencia_pago' => 'nullable|string|max:100',
            'detalle_pago' => 'nullable|array',
            'monto_efectivo' => 'nullable|numeric|min:0',
            'monto_otro' => 'nullable|numeric|min:0',
            'cambio' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ============================================================
        // VERIFICAR CAJA ABIERTA
        // ============================================================
        $tenantId = auth()->user()->tenant_id ?? null;

        $caja = Caja::where('tenant_id', $tenantId)
            ->where('estado', 'abierta')
            ->first();

        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay una caja abierta. Debe abrir caja antes de registrar ventas.'
            ], 422);
        }

        // ============================================================
        // CALCULAR TOTALES
        // ============================================================
        $subtotal = 0;
        $ivaTotal = 0;

        foreach ($request->productos as $item) {
            $subtotalItem = $item['precio'] * $item['cantidad'];
            $subtotal += $subtotalItem;
            $ivaItem = $subtotalItem * 0.19;
            $ivaTotal += $ivaItem;
        }

        $descuentoPorcentaje = $request->descuento_porcentaje ?? 0;
        $descuento = $subtotal * ($descuentoPorcentaje / 100);
        $total = $subtotal - $descuento + $ivaTotal;

        try {
            DB::beginTransaction();

            // ============================================================
            // CREAR VENTA
            // ============================================================
            $venta = Venta::create([
                'tenant_id' => $tenantId,
                'cliente_id' => $request->cliente_id,
                'mascota_id' => $request->mascota_id,
                'usuario_id' => auth()->id(),
                'numero_factura' => 'V-' . str_pad(Venta::where('tenant_id', $tenantId)->count() + 1, 6, '0', STR_PAD_LEFT),
                'fecha' => now(),
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'iva' => $ivaTotal,
                'total' => $total,
                'metodo_pago' => $request->metodo_pago ?? 'efectivo',
                'estado' => 'completada',
                'observaciones' => $request->observaciones,

                // 🔥 CAMPOS DE PAGO NUEVOS
                'referencia_pago' => $request->referencia_pago,
                'detalle_pago' => $request->detalle_pago,
                'monto_efectivo' => $request->monto_efectivo,
                'monto_otro' => $request->monto_otro,
                'cambio' => $request->cambio,
            ]);

            // ============================================================
            // CREAR DETALLES Y ACTUALIZAR STOCK
            // ============================================================
            foreach ($request->productos as $item) {
                $producto = Producto::find($item['id']);

                if (!$producto) {
                    throw new \Exception('Producto no encontrado: ' . $item['id']);
                }

                $subtotalItem = $item['precio'] * $item['cantidad'];
                $ivaItem = $subtotalItem * 0.19;

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $subtotalItem,
                    'iva' => $ivaItem,
                    'total' => $subtotalItem + $ivaItem,
                ]);

                // Descontar stock solo si es producto
                if ($producto->tipo === 'producto' || $producto->tipo === null) {
                    $producto->stock -= $item['cantidad'];
                    $producto->save();
                }
            }

            // ============================================================
            // ACTUALIZAR CAJA
            // ============================================================
            $saldoAnterior = $caja->saldo_actual;
            $saldoNuevo = $saldoAnterior + $total;

            $caja->update(['saldo_actual' => $saldoNuevo]);

            // ============================================================
            // REGISTRAR MOVIMIENTO DE CAJA
            // ============================================================
            $apertura = DB::table('caja_aperturas')
                ->where('caja_id', $caja->id)
                ->where('estado', 'abierta')
                ->orderBy('fecha_apertura', 'desc')
                ->first();

            CajaMovimiento::create([
                'caja_id' => $caja->id,
                'caja_apertura_id' => $apertura ? $apertura->id : null,
                'usuario_id' => auth()->id(),
                'venta_id' => $venta->id,
                'tipo' => 'ingreso',
                'categoria' => 'venta',
                'monto' => $total,
                'saldo_anterior' => $saldoAnterior,
                'saldo_nuevo' => $saldoNuevo,
                'metodo_pago' => $request->metodo_pago ?? 'efectivo',
                'referencia' => $request->referencia_pago,
                'descripcion' => 'Venta #' . $venta->numero_factura,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada exitosamente',
                'venta' => $venta->fresh(),
                'numero' => $venta->numero_factura,
                'total' => $total,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar clientes (AJAX)
     */
    public function buscarClientes(Request $request)
    {
        $query = $request->get('q', '');
        $tenantId = auth()->user()->tenant_id ?? null;

        $clientes = Cliente::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->where(function($q) use ($query) {
                $q->where('nombres', 'LIKE', "%{$query}%")
                  ->orWhere('apellidos', 'LIKE', "%{$query}%")
                  ->orWhere('numero_documento', 'LIKE', "%{$query}%")
                  ->orWhere('celular', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($clientes);
    }

    /**
     * Buscar mascotas (AJAX)
     */
    public function buscarMascotas(Request $request)
    {
        $query = $request->get('q', '');
        $clienteId = $request->get('cliente_id');
        $tenantId = auth()->user()->tenant_id ?? null;

        $mascotas = Mascota::whereHas('cliente', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->when($clienteId, function($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            })
            ->where('activo', true)
            ->where(function($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('raza', 'LIKE', "%{$query}%")
                  ->orWhere('especie', 'LIKE', "%{$query}%");
            })
            ->with('cliente')
            ->limit(10)
            ->get();

        return response()->json($mascotas);
    }

    /**
     * Listar ventas (DataTable)
     */
    public function index()
    {
        return view('ventas');
    }

    /**
     * DataTable de ventas
     */
    public function datatable(Request $request)
    {
        $ventas = Venta::with(['cliente', 'usuario'])
            ->where('tenant_id', auth()->user()->tenant_id ?? null);

        return DataTables::of($ventas)
            ->addColumn('cliente_nombre', function($venta) {
                return $venta->cliente 
                    ? $venta->cliente->nombres . ' ' . $venta->cliente->apellidos 
                    : 'Consumidor Final';
            })
            ->addColumn('usuario_nombre', function($venta) {
                return $venta->usuario->name ?? 'N/A';
            })
            ->addColumn('fecha_formato', function($venta) {
                return $venta->fecha ? $venta->fecha->format('d/m/Y H:i') : 'N/A';
            })
            ->addColumn('total_formateado', function($venta) {
                return '$ ' . number_format($venta->total, 0, ',', '.');
            })
            ->addColumn('metodo_pago_label', function($venta) {
                $metodos = [
                    'efectivo' => 'Efectivo',
                    'tarjeta' => 'Tarjeta',
                    'tarjeta_credito' => 'Tarjeta Crédito',
                    'tarjeta_debito' => 'Tarjeta Débito',
                    'transferencia' => 'Transferencia',
                    'mixto' => 'Mixto',
                    'nequi' => 'Nequi',
                    'daviplata' => 'Daviplata',
                ];
                return $metodos[$venta->metodo_pago] ?? ucfirst($venta->metodo_pago);
            })
            ->addColumn('estado_badge', function($venta) {
                $estados = [
                    'pendiente' => 'warning',
                    'completada' => 'success',
                    'anulada' => 'danger',
                ];
                $color = $estados[$venta->estado] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . ucfirst($venta->estado) . '</span>';
            })
            ->addColumn('acciones', function($venta) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-primary btn-accion" onclick="verVenta(' . $venta->id . ')" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="/ventas/' . $venta->id . '/pdf" class="btn btn-danger btn-accion" target="_blank" title="PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['estado_badge', 'acciones'])
            ->make(true);
    }

    /**
     * Ver detalle de venta
     */
    public function show($id)
    {
        $venta = Venta::with(['cliente', 'mascota', 'usuario', 'detalles.producto'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'venta' => $venta,
        ]);
    }

    /**
     * Eliminar venta
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($id);

            // Revertir stock
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto && ($producto->tipo === 'producto' || $producto->tipo === null)) {
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                }
            }

            // Eliminar detalles
            $venta->detalles()->delete();

            // Eliminar venta
            $venta->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF de venta
     */
    public function generarPDF($id)
    {
        $venta = Venta::with(['cliente', 'mascota', 'usuario', 'detalles.producto'])
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ventas.pdf', compact('venta'))
            ->setPaper('letter');

        $nombreArchivo = 'venta-' . $venta->numero_factura . '.pdf';

        return $pdf->download($nombreArchivo);
    }

  public function datosImpresion(Venta $venta)
{
    $venta->load(['detalles', 'cliente', 'mascota', 'usuario', 'tenant']);

    return response()->json([
        'success' => true,
        'venta'   => $venta,
    ]);

    return response()->json([
        'success' => true,
        'venta'   => $venta,
    ]);
}

}