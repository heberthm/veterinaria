<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        $categorias = CategoriaProducto::orderBy('nombre')->get();

        return view('inventario', compact('categorias'));
    }

    public function datatable()
    {
        $productos = Producto::with('categoria')->orderByDesc('created_at')->get();

        return response()->json([
            'data' => $productos->map(fn ($p) => [
                'id'             => $p->id,
                'codigo'         => $p->codigo,
                'nombre'         => $p->nombre,
                'categoria'      => $p->categoria?->nombre,
                'tipo'           => $p->tipo,
                'precio_compra'  => $p->precio_compra,
                'precio_venta'   => $p->precio_venta,
                'iva_porcentaje' => $p->iva_porcentaje,
                'stock'          => $p->stock,
                'stock_minimo'   => $p->stock_minimo,
                'stock_bajo'     => $p->stock <= $p->stock_minimo,
                'activo'         => $p->activo,
            ]),
        ]);
    }

    public function show($id)
    {
        $producto = Producto::findOrFail($id);

        return response()->json($producto);
    }

    public function store(Request $request)
    {
        $validado = $this->validarDatos($request);

        $producto = Producto::create($validado);

        return response()->json([
            'success'  => true,
            'message'  => 'Producto registrado correctamente.',
            'producto' => $producto->load('categoria'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $validado = $this->validarDatos($request);

        $producto->update($validado);

        return response()->json([
            'success'  => true,
            'message'  => 'Producto actualizado correctamente.',
            'producto' => $producto->load('categoria'),
        ]);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        // Producto usa SoftDeletes: esto no borra el registro físicamente,
        // así que el historial de ventas/compras que lo referencian queda intacto.
        $producto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.',
        ]);
    }

    /**
     * Ajuste manual de stock (entrada o salida), independiente de una
     * compra o venta — por ejemplo, para corregir un conteo físico.
     */
    public function ajustarStock(Request $request, $id)
    {
        $request->validate([
            'tipo'     => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo'   => 'nullable|string|max:255',
        ]);

        $producto = Producto::findOrFail($id);

        if ($request->tipo === 'entrada') {
            $producto->increment('stock', $request->cantidad);
        } else {
            if ($producto->stock < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => "No puedes descontar {$request->cantidad} unidades: solo hay {$producto->stock} en stock.",
                ], 422);
            }
            $producto->decrement('stock', $request->cantidad);
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Stock ajustado correctamente.',
            'nuevo_stock'  => $producto->fresh()->stock,
        ]);
    }

    protected function validarDatos(Request $request): array
    {
        return $request->validate([
            'categoria_id'    => 'nullable|exists:categorias_productos,id',
            'codigo'          => 'nullable|string|max:50',
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'tipo'            => 'required|in:producto,servicio,paquete',
            'precio_compra'   => 'required|numeric|min:0',
            'precio_venta'    => 'required|numeric|min:0',
            'iva_porcentaje'  => 'required|numeric|min:0|max:100',
            'stock'           => 'required_if:tipo,producto|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'unidad_medida'   => 'nullable|string|max:50',
            'activo'          => 'sometimes|boolean',
        ]);
    }
}
