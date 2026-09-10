<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CategoriaProducto;
use App\Models\Cliente;
use App\Models\Mascota;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VentaController extends Controller
{
    public function pos()
    {
        $categorias = CategoriaProducto::orderBy('nombre')->get();

        $productos = Producto::where('activo', true)
            ->where('tipo', 'producto')
            ->orderBy('nombre')
            ->get();

        $servicios = Producto::where('activo', true)
            ->where('tipo', 'servicio')
            ->orderBy('nombre')
            ->get();

        $paquetes = Producto::where('activo', true)
            ->where('tipo', 'paquete')
            ->orderBy('nombre')
            ->get();

      
        $caja = Caja::where('tenant_id', auth()->user()->tenant_id ?? null)
             ->where('usuario_id', auth()->id())
            ->where('estado', 'abierta')
             ->orderBy('fecha_apertura', 'desc')
            ->first();

        $cajaActual = Caja::where('usuario_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        $siguienteConsecutivo = 'POS-' . str_pad(
            (Venta::count() + 1),
            6, '0', STR_PAD_LEFT
        );

        return view('ventas-pos', compact(
            'categorias', 'productos', 'servicios', 'paquetes',
            'cajaActual', 'siguienteConsecutivo'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'                     => 'required|array|min:1',
            'items.*.producto_id'       => 'required|exists:productos,id',
            'items.*.cantidad'          => 'required|integer|min:1',
            'cliente_id'                => 'nullable|exists:clientes,id',
            'mascota_id'                => 'nullable|exists:mascotas,id',
            'descuento_porcentaje'      => 'nullable|numeric|min:0|max:100',
            'metodo_pago'               => 'required|in:efectivo,tarjeta,transferencia,mixto',
            'observacion'               => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cajaActual = Caja::where('usuario_id', Auth::id())->where('estado', 'abierta')->first();
        if (!$cajaActual) {
            return response()->json([
                'success' => false,
                'message' => 'Debes abrir la caja antes de registrar una venta.',
            ], 422);
        }

        $venta = DB::transaction(function () use ($request, $cajaActual) {
            $subtotal = 0;
            $ivaValor = 0;
            $itemsValidados = [];

            foreach ($request->items as $item) {
                $producto = Producto::findOrFail($item['producto_id']);

                if ($producto->stock < $item['cantidad']) {
                    abort(422, "Stock insuficiente para \"{$producto->nombre}\" (disponible: {$producto->stock}).");
                }

                $subtotalItem = $producto->precio_venta * $item['cantidad'];
                $ivaItem = round($subtotalItem * ($producto->iva_porcentaje / 100), 2);

                $subtotal += $subtotalItem;
                $ivaValor += $ivaItem;

                $itemsValidados[] = [
                    'producto'  => $producto,
                    'cantidad'  => $item['cantidad'],
                    'subtotal'  => $subtotalItem,
                ];
            }

            $descuentoPorcentaje = $request->input('descuento_porcentaje', 0);
            $descuentoValor = round($subtotal * ($descuentoPorcentaje / 100), 2);
            $total = round($subtotal - $descuentoValor + $ivaValor, 2);

            $consecutivo = 'POS-' . str_pad((Venta::count() + 1), 6, '0', STR_PAD_LEFT);

            $venta = Venta::create([
                'caja_id'               => $cajaActual->id,
                'consecutivo'            => $consecutivo,
                'cliente_id'             => $request->cliente_id,
                'mascota_id'             => $request->mascota_id,
                'usuario_id'                => Auth::id(),
                'subtotal'               => $subtotal,
                'descuento_porcentaje'   => $descuentoPorcentaje,
                'descuento_valor'        => $descuentoValor,
                'iva_valor'              => $ivaValor,
                'total'                  => $total,
                'metodo_pago'            => $request->metodo_pago,
                'estado'                 => 'pagada',
                'observacion'            => $request->observacion,
            ]);

            foreach ($itemsValidados as $item) {
                VentaItem::create([
                    'venta_id'         => $venta->id,
                    'producto_id'      => $item['producto']->id,
                    'nombre_producto'  => $item['producto']->nombre,
                    'precio_unitario'  => $item['producto']->precio_venta,
                    'cantidad'         => $item['cantidad'],
                    'iva_porcentaje'   => $item['producto']->iva_porcentaje,
                    'subtotal'         => $item['subtotal'],
                ]);

                $item['producto']->decrement('stock', $item['cantidad']);
            }

            return $venta;
        });

        return response()->json([
            'success' => true,
            'message' => 'Venta registrada correctamente.',
            'venta'   => $venta->load('items'),
        ]);
    }

    public function buscarClientes(Request $request)
    {
        $termino = $request->input('q', '');

        $clientes = Cliente::where('activo', true)
            ->where(function ($query) use ($termino) {
                $query->where('nombres', 'like', "%{$termino}%")
                    ->orWhere('apellidos', 'like', "%{$termino}%")
                    ->orWhere('numero_documento', 'like', "%{$termino}%")
                    ->orWhere('telefono', 'like', "%{$termino}%");
            })
            ->limit(10)
            ->get(['id', 'nombres', 'apellidos', 'numero_documento', 'telefono']);

        return response()->json($clientes->map(fn ($c) => [
            'id'    => $c->id,
            'texto' => $c->nombreCompleto() . ' — ' . $c->numero_documento,
        ]));
    }

    public function buscarMascotas(Request $request)
    {
        $termino = $request->input('q', '');
        $clienteId = $request->input('cliente_id');

        $mascotas = Mascota::query()
            ->when($clienteId, fn ($q) => $q->where('cliente_id', $clienteId))
            ->where('nombre', 'like', "%{$termino}%")
            ->limit(10)
            ->get(['id', 'nombre', 'cliente_id']);

        return response()->json($mascotas->map(fn ($m) => [
            'id'    => $m->id,
            'texto' => $m->nombre,
        ]));
    }
}
