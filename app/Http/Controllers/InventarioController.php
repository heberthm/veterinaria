<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InventarioController extends Controller
{
    public function index()
    {
        $categorias = CategoriaProducto::orderBy('nombre')->get();

        return view('inventario', compact('categorias'));
    }

       public function datatable()
    {
        $productos = Producto::with('categoria')->orderByDesc('created_at');

        return DataTables::of($productos)
            ->addColumn('categoria_nombre', function($p) {
                return $p->categoria ? $p->categoria->nombre : '—';
            })
            ->addColumn('precio_venta_fmt', function($p) {
                return '$ ' . number_format($p->precio_venta, 0, ',', '.');
            })
            ->addColumn('estado_badge', function($p) {
                return $p->activo 
                    ? '<span class="badge badge-success">Activo</span>' 
                    : '<span class="badge badge-secondary">Inactivo</span>';
            })
            ->addColumn('acciones', function($p) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="verProducto(' . $p->id . ')" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning" onclick="editarProducto(' . $p->id . ')" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger" onclick="eliminarProducto(' . $p->id . ')" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['estado_badge', 'acciones'])
            ->make(true);
    }

        public function store(Request $request)
    {
        // 1. Validar los datos
        $validado = $this->validarDatos($request);

        // 2. Agregar el tenant_id del usuario logueado
        $validado['tenant_id'] = auth()->user()->tenant_id ?? 1;

        // 3. Crear el producto
        $producto = Producto::create($validado);

        return response()->json([
            'success'  => true,
            'message'  => 'Producto registrado correctamente.',
            'producto' => $producto->load('categoria'),
        ]);
    }
  

        public function show($id)
    {
        try {
            $producto = Producto::with('categoria')->findOrFail($id);

            return response()->json([
                'success' => true,
                'producto' => $producto
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
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
