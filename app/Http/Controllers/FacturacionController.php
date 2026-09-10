<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\FacturaPago;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FacturacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver_facturas')->only(['index', 'show', 'detalle']);
        $this->middleware('permission:crear_facturas')->only(['create', 'store']);
        $this->middleware('permission:editar_facturas')->only(['edit', 'update']);
        $this->middleware('permission:eliminar_facturas')->only(['destroy']);
    }

    /**
     * Mostrar lista de facturas
     */
    public function index()
    {
        return view('facturas');
    }

    /**
     * DataTable para facturas
     */
    public function datatable(Request $request)
    {
        $facturas = Factura::with(['cliente', 'usuario'])
            ->where('tenant_id', auth()->user()->tenant_id ?? null);

        return DataTables::of($facturas)
            ->addColumn('cliente_nombre', function($factura) {
                return $factura->cliente->nombres . ' ' . $factura->cliente->apellidos;
            })
            ->addColumn('fecha_emision_formato', function($factura) {
                return $factura->fecha_emision->format('d/m/Y');
            })
            ->addColumn('estado_badge', function($factura) {
                $estados = [
                    'pendiente' => 'warning',
                    'pagada' => 'success',
                    'anulada' => 'danger',
                    'vencida' => 'dark'
                ];
                return '<span class="badge badge-' . ($estados[$factura->estado] ?? 'secondary') . '">' 
                    . Factura::ESTADOS[$factura->estado] . '</span>';
            })
            ->addColumn('total_formateado', function($factura) {
                return '$ ' . number_format($factura->total, 0, ',', '.');
            })
            ->addColumn('saldo_formateado', function($factura) {
                return '$ ' . number_format($factura->saldo, 0, ',', '.');
            })
            ->addColumn('acciones', function($factura) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-primary btn-accion" onclick="verFactura(' . $factura->id . ')" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-success btn-accion" onclick="registrarPago(' . $factura->id . ')" title="Registrar pago">
                            <i class="fas fa-money-bill-wave"></i>
                        </button>
                        <button class="btn btn-danger btn-accion" onclick="eliminarFactura(' . $factura->id . ')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['estado_badge', 'acciones'])
            ->make(true);
    }

    /**
     * Mostrar formulario de creación (modal)
     */
    public function create(Request $request)
    {
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id ?? null)
            ->where('activo', true)
            ->get();
        
        $productos = Producto::where('tenant_id', auth()->user()->tenant_id ?? null)
            ->where('stock', '>', 0)
            ->get();

        $ventaId = $request->get('venta_id', null);
        $venta = null;
        $clienteId = null;

        if ($ventaId) {
            $venta = Venta::with(['cliente', 'detalles'])->find($ventaId);
            if ($venta) {
                $clienteId = $venta->cliente_id;
            }
        }

        return view('modales.factura-form', compact('clientes', 'productos', 'venta', 'ventaId', 'clienteId'));
    }

    /**
     * Guardar nueva factura
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'tipo' => 'required|in:venta,servicio,consulta,proforma',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subtotal = 0;
        $iva = 0;
        $total = 0;

        // Calcular totales
        foreach ($request->productos as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }

        $iva = $subtotal * 0.19;
        $total = $subtotal + $iva;

        // Crear factura
        $factura = Factura::create([
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'cliente_id' => $request->cliente_id,
            'venta_id' => $request->venta_id ?? null,
            'numero_factura' => Factura::generarNumeroFactura(),
            'fecha_emision' => $request->fecha_emision,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'tipo' => $request->tipo,
            'estado' => 'pendiente',
            'subtotal' => $subtotal,
            'iva' => $iva,
            'descuento' => 0,
            'total' => $total,
            'abonado' => 0,
            'saldo' => $total,
            'observaciones' => $request->observaciones,
            'usuario_id' => auth()->id(),
        ]);

        // Crear detalles
        foreach ($request->productos as $item) {
            $producto = Producto::find($item['id']);
            $precio = $item['precio'];
            $cantidad = $item['cantidad'];
            $subtotalItem = $precio * $cantidad;
            $ivaItem = $subtotalItem * 0.19;
            $totalItem = $subtotalItem + $ivaItem;

            FacturaDetalle::create([
                'factura_id' => $factura->id,
                'producto_id' => $item['id'],
                'descripcion' => $producto->nombre,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'descuento' => 0,
                'iva' => $ivaItem,
                'total' => $totalItem,
                'tipo' => 'producto',
            ]);

            // Actualizar stock
            $producto->stock -= $cantidad;
            $producto->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Factura creada exitosamente',
            'factura' => $factura
        ]);
    }

    /**
     * Mostrar detalle de factura (modal)
     */
    public function detalle($id)
    {
        $factura = Factura::with(['cliente', 'usuario', 'detalles.producto', 'pagos'])
            ->findOrFail($id);

        return view('modales.factura-detalle', compact('factura'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $factura = Factura::with(['detalles'])->findOrFail($id);
        $clientes = Cliente::where('tenant_id', auth()->user()->tenant_id ?? null)->get();
        $productos = Producto::where('tenant_id', auth()->user()->tenant_id ?? null)->get();

        return view('facturas-editar', compact('factura', 'clientes', 'productos'));
    }

    /**
     * Actualizar factura
     */
    public function update(Request $request, $id)
    {
        $factura = Factura::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'tipo' => 'required|in:venta,servicio,consulta,proforma',
            'estado' => 'required|in:pendiente,pagada,anulada,vencida',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $factura->update($request->only([
            'cliente_id', 'fecha_emision', 'fecha_vencimiento', 
            'tipo', 'estado', 'observaciones'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Factura actualizada exitosamente'
        ]);
    }

    /**
     * Eliminar factura
     */
    public function destroy($id)
    {
        $factura = Factura::findOrFail($id);

        if ($factura->estado === 'pagada') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar una factura pagada'
            ], 422);
        }

        // Revertir stock de productos
        foreach ($factura->detalles as $detalle) {
            if ($detalle->producto_id) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                }
            }
        }

        $factura->detalles()->delete();
        $factura->pagos()->delete();
        $factura->delete();

        return response()->json([
            'success' => true,
            'message' => 'Factura eliminada exitosamente'
        ]);
    }

    /**
     * Registrar pago
     */
    public function registrarPago(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta_credito,tarjeta_debito,transferencia,nequi,daviplata,qr',
            'referencia' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $factura = Factura::findOrFail($id);

        if ($factura->estaPagada()) {
            return response()->json([
                'success' => false,
                'message' => 'La factura ya está pagada'
            ], 422);
        }

        $monto = $request->monto;
        $saldoRestante = $factura->calcularSaldo();

        if ($monto > $saldoRestante) {
            return response()->json([
                'success' => false,
                'message' => 'El monto excede el saldo pendiente'
            ], 422);
        }

        // Registrar pago
        $pago = $factura->registrarPago(
            $monto,
            $request->metodo_pago,
            $request->referencia,
            $request->observaciones
        );

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado exitosamente',
            'pago' => $pago,
            'saldo_restante' => $factura->saldo
        ]);
    }

    /**
     * Descargar PDF de factura
     */
    public function generarPDF($id)
    {
        $factura = Factura::with(['cliente', 'usuario', 'detalles.producto', 'pagos'])
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('facturas-pdf', compact('factura'));
        return $pdf->download('factura-' . $factura->numero_factura . '.pdf');
    }

    /**
     * Obtener datos de factura para edición
     */
    public function show($id)
    {
        $factura = Factura::with(['cliente', 'detalles.producto', 'pagos'])
            ->findOrFail($id);
        return response()->json($factura);
    }
}