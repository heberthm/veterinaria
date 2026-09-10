<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->where('tipo', 'producto')->orderBy('nombre')->get();

        return view('compras', compact('proveedores', 'productos'));
    }

    public function datatable()
    {
        $compras = Compra::with('proveedor')->orderByDesc('fecha_compra')->get();

        return response()->json([
            'data' => $compras->map(fn ($c) => [
                'id'          => $c->id,
                'proveedor'   => $c->proveedor?->nombre,
                'numero_factura_proveedor' => $c->numero_factura_proveedor,
                'fecha_compra' => $c->fecha_compra->format('d/m/Y'),
                'total'       => $c->total,
                'estado'      => $c->estado,
            ]),
        ]);
    }

    public function show($id)
    {
        $compra = Compra::with(['items.producto', 'proveedor'])->findOrFail($id);

        return response()->json($compra);
    }

    public function detalle($id)
    {
        $compra = Compra::with(['items.producto', 'proveedor'])->findOrFail($id);

        return response()->json($compra);
    }

    public function store(Request $request)
    {
        $validado = $request->validate([
            'proveedor_id'              => 'required|exists:proveedores,id',
            'numero_factura_proveedor'  => 'nullable|string|max:100',
            'fecha_compra'              => 'required|date',
            'observacion'               => 'nullable|string',
            'items'                     => 'required|array|min:1',
            'items.*.producto_id'       => 'required|exists:productos,id',
            'items.*.cantidad'          => 'required|integer|min:1',
            'items.*.precio_unitario'   => 'required|numeric|min:0',
        ]);

        $compra = DB::transaction(function () use ($validado) {
            $subtotal = 0;
            foreach ($validado['items'] as $item) {
                $subtotal += $item['cantidad'] * $item['precio_unitario'];
            }
            // IVA se toma del producto (ya lo maneja Inventario); aquí usamos
            // un 19% general sobre el subtotal por simplicidad de captura.
            $ivaValor = round($subtotal * 0.19, 2);
            $total = $subtotal + $ivaValor;

            $compra = Compra::create([
                'proveedor_id'             => $validado['proveedor_id'],
                'user_id'                  => Auth::id(),
                'numero_factura_proveedor' => $validado['numero_factura_proveedor'] ?? null,
                'fecha_compra'             => $validado['fecha_compra'],
                'subtotal'                 => $subtotal,
                'iva_valor'                => $ivaValor,
                'total'                    => $total,
                'estado'                   => 'pendiente',
                'observacion'              => $validado['observacion'] ?? null,
            ]);

            foreach ($validado['items'] as $item) {
                CompraItem::create([
                    'compra_id'        => $compra->id,
                    'producto_id'      => $item['producto_id'],
                    'cantidad'         => $item['cantidad'],
                    'precio_unitario'  => $item['precio_unitario'],
                    'subtotal'         => $item['cantidad'] * $item['precio_unitario'],
                ]);
            }

            return $compra;
        });

        return response()->json([
            'success' => true,
            'message' => 'Compra registrada como pendiente de recepción.',
            'compra'  => $compra->load('items'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes editar compras que sigan pendientes de recepción.',
            ], 422);
        }

        $validado = $request->validate([
            'proveedor_id'              => 'required|exists:proveedores,id',
            'numero_factura_proveedor'  => 'nullable|string|max:100',
            'fecha_compra'              => 'required|date',
            'observacion'               => 'nullable|string',
            'items'                     => 'required|array|min:1',
            'items.*.producto_id'       => 'required|exists:productos,id',
            'items.*.cantidad'          => 'required|integer|min:1',
            'items.*.precio_unitario'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($compra, $validado) {
            $subtotal = collect($validado['items'])->sum(fn ($i) => $i['cantidad'] * $i['precio_unitario']);
            $ivaValor = round($subtotal * 0.19, 2);

            $compra->update([
                'proveedor_id'             => $validado['proveedor_id'],
                'numero_factura_proveedor' => $validado['numero_factura_proveedor'] ?? null,
                'fecha_compra'             => $validado['fecha_compra'],
                'subtotal'                 => $subtotal,
                'iva_valor'                => $ivaValor,
                'total'                    => $subtotal + $ivaValor,
                'observacion'              => $validado['observacion'] ?? null,
            ]);

            $compra->items()->delete();
            foreach ($validado['items'] as $item) {
                CompraItem::create([
                    'compra_id'        => $compra->id,
                    'producto_id'      => $item['producto_id'],
                    'cantidad'         => $item['cantidad'],
                    'precio_unitario'  => $item['precio_unitario'],
                    'subtotal'         => $item['cantidad'] * $item['precio_unitario'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Compra actualizada correctamente.',
        ]);
    }

    /**
     * Marca la compra como recibida y suma el stock de cada producto.
     * Es la única acción que efectivamente mueve inventario.
     */
    public function recibir($id)
    {
        $compra = Compra::with('items')->findOrFail($id);

        if ($compra->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Esta compra ya fue recibida o está anulada.',
            ], 422);
        }

        DB::transaction(function () use ($compra) {
            foreach ($compra->items as $item) {
                Producto::whereKey($item->producto_id)->increment('stock', $item->cantidad);
            }
            $compra->update(['estado' => 'recibida']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Compra recibida: el stock de los productos ya fue actualizado.',
        ]);
    }

    /**
     * Anula la compra (nunca se borra físicamente para no perder el
     * historial). Si ya estaba recibida, no se permite anular aquí
     * para no descuadrar el stock ya sumado.
     */
    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->estado === 'recibida') {
            return response()->json([
                'success' => false,
                'message' => 'No puedes anular una compra ya recibida (el stock ya fue actualizado).',
            ], 422);
        }

        $compra->update(['estado' => 'anulada']);

        return response()->json([
            'success' => true,
            'message' => 'Compra anulada.',
        ]);
    }

    /**
     * Alta rápida de proveedor desde el mismo modal de Nueva Compra,
     * sin necesitar un módulo de Proveedores aparte todavía.
     */
    public function storeProveedor(Request $request)
    {
        $validado = $request->validate([
            'nombre'    => 'required|string|max:150',
            'nit'       => 'nullable|string|max:50',
            'telefono'  => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',
        ]);

        $proveedor = Proveedor::create($validado + ['activo' => true]);

        return response()->json([
            'success'    => true,
            'proveedor'  => $proveedor,
        ]);
    }
}
