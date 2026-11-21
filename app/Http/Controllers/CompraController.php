<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables; // 🔥 Asegúrate de tenerlo

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
        $compras = \Illuminate\Support\Facades\DB::table('compras as c')
            ->leftJoin('proveedores as p', 'p.id', '=', 'c.proveedor_id')
            ->select([
                'c.id',
                'c.numero_factura_proveedor',
                'p.nombre as proveedor',
                'c.fecha_compra',
                'c.fecha_vencimiento', // 🔥 Ahora sí se envía
                'c.total',
                'c.estado',
            ])
            ->orderByDesc('c.fecha_compra');

        return DataTables::of($compras)
            ->addColumn('fecha_compra', function($c) {
                return $c->fecha_compra ? \Carbon\Carbon::parse($c->fecha_compra)->format('d/m/Y') : '—';
            })
            ->addColumn('fecha_vencimiento', function($c) {
                return $c->fecha_vencimiento ? \Carbon\Carbon::parse($c->fecha_vencimiento)->format('d/m/Y') : '—';
            })
            ->addColumn('total', function($c) {
                return '$ ' . number_format($c->total, 0, ',', '.');
            })
            ->addColumn('estado', function($c) {
                $colores = ['pendiente' => 'warning', 'recibida' => 'success', 'anulada' => 'danger'];
                $color = $colores[$c->estado] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . ucfirst($c->estado) . '</span>';
            })
            ->addColumn('acciones', function($c) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="verCompra(' . $c->id . ')" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-danger" onclick="eliminarCompra(' . $c->id . ')" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['estado', 'acciones'])
            ->make(true);
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
        try {
            $validado = $request->validate([
                'proveedor_id'              => 'required|exists:proveedores,id',
                'numero_factura'            => 'nullable|string|max:18',
                'fecha_compra'              => 'required|date',
                 'fecha_vencimiento'        => 'required|date',
                'observacion'               => 'nullable|string',
                'items'                     => 'required|array|min:1',
                'items.*.producto_id'       => 'required|exists:productos,id',
                'items.*.cantidad'          => 'required|integer|min:1',
                'items.*.precio_unitario'   => 'required|numeric|min:0',
            ]);

            $compra = DB::transaction(function () use ($validado) {
                $subtotal = 0;
                $itemsParaJson = [];

                foreach ($validado['items'] as $item) {
                    $subtotal += $item['cantidad'] * $item['precio_unitario'];
                    
                    $itemsParaJson[] = [
                        'producto_id'     => $item['producto_id'],
                        'cantidad'        => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal'        => $item['cantidad'] * $item['precio_unitario'],
                    ];
                }

                $ivaValor = round($subtotal * 0.19, 2);
                $total = $subtotal + $ivaValor;

                $compra = Compra::create([
                    'tenant_id'                => auth()->user()->tenant_id ?? 1,
                    'proveedor_id'             => $validado['proveedor_id'],
                    'user_id'                  => Auth::id(),
                    'numero_factura_proveedor' => $validado['numero_factura'] ?? null,
                    'fecha_compra'             => $validado['fecha_compra'],
                    'fecha_vencimiento'       => $validado['fecha_vencimiento'],
                    'metodo_pago'             => $validado['metodo_pago'],
                    'subtotal'                 => $subtotal,
                    'iva_valor'                => $ivaValor,
                    'total'                    => $total,
                    'items_json'               => $itemsParaJson, // 🔥 Se guarda el JSON completo aquí
                    'estado'                   => 'pendiente',
                    'observacion'              => $validado['observacion'] ?? null,
                ]);

                return $compra;
            });

            return response()->json([
                'success' => true,
                'message' => 'Compra registrada correctamente.',
                'compra'  => $compra,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
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
            'numero_factura'            => 'nullable|string|max:100',
            'fecha_compra'              => 'required|date',
            'fecha_vencimiento'         => 'nullable|date',
            'metodo_pago'               => 'nullable|string',
            'observacion'               => 'nullable|string',
            'items'                     => 'required|array|min:1',
            'items.*.producto_id'       => 'required', // 🔥 Ya no validamos que exista, solo que venga
            'items.*.cantidad'          => 'required|numeric|min:0.01',
            'items.*.precio_unitario'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($compra, $validado) {
            $subtotal = 0;
            $itemsParaJson = [];

            foreach ($validado['items'] as $item) {
                $subtotal += $item['cantidad'] * $item['precio_unitario'];
                $itemsParaJson[] = [
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'        => $item['cantidad'] * $item['precio_unitario'],
                ];
            }

            $ivaValor = round($subtotal * 0.19, 2);

            $compra->update([
                'proveedor_id'             => $validado['proveedor_id'],
                'numero_factura_proveedor' => $validado['numero_factura'] ?? null,
                'fecha_compra'             => $validado['fecha_compra'],
                'fecha_vencimiento'        => $validado['fecha_vencimiento'] ?? null,
                'metodo_pago'              => $validado['metodo_pago'] ?? null,
                'subtotal'                 => $subtotal,
                'iva_valor'                => $ivaValor,
                'total'                    => $subtotal + $ivaValor,
                'items_json'               => $itemsParaJson, // 🔥 Guardamos el JSON actualizado
                'observacion'              => $validado['observacion'] ?? null,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Compra actualizada correctamente.',
        ]);
    }

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

    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);

        if ($compra->estado === 'recibida') {
            return response()->json([
                'success' => false,
                'message' => 'No puedes anular una compra ya recibida.',
            ], 422);
        }

        $compra->update(['estado' => 'anulada']);

        return response()->json([
            'success' => true,
            'message' => 'Compra anulada.',
        ]);
    }

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