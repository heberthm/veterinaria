<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function ventas(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $ventas = Venta::with(['cliente', 'detalles.producto'])
            ->where('tenant_id', $request->tenant->id)
            ->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin])
            ->get();

        $total_ventas = $ventas->sum('total');
        $total_iva = $ventas->sum('iva');
        $total_productos = $ventas->sum(function($venta) {
            return $venta->detalles->sum('cantidad');
        });

        // Productos más vendidos
        $productos_mas_vendidos = Producto::where('tenant_id', $request->tenant->id)
            ->withCount(['detalleVentas as total_vendido' => function($query) use ($request) {
                $query->whereHas('venta', function($q) use ($request) {
                    $q->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin]);
                });
            }])
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        return view('reportes.ventas', compact(
            'ventas', 
            'total_ventas', 
            'total_iva', 
            'total_productos',
            'productos_mas_vendidos'
        ));
    }

    public function citas(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $citas = Cita::with(['cliente', 'mascota', 'veterinario'])
            ->where('tenant_id', $request->tenant->id)
            ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin])
            ->get();

        $total_citas = $citas->count();
        $citas_por_estado = $citas->groupBy('estado')->map->count();
        $citas_por_tipo = $citas->groupBy('tipo')->map->count();
        $ingresos_citas = $citas->where('estado_pago', 'pagado')->sum('costo');

        return view('reportes.citas', compact(
            'citas',
            'total_citas',
            'citas_por_estado',
            'citas_por_tipo',
            'ingresos_citas'
        ));
    }

    public function inventario(Request $request)
    {
        $productos = Producto::where('tenant_id', $request->tenant->id)->get();
        
        $stock_bajo = $productos->filter(function($producto) {
            return $producto->stock <= $producto->stock_minimo;
        });

        $stock_agotado = $productos->filter(function($producto) {
            return $producto->stock == 0;
        });

        $valor_inventario = $productos->sum(function($producto) {
            return $producto->stock * $producto->precio_compra;
        });

        return view('reportes.inventario', compact(
            'productos',
            'stock_bajo',
            'stock_agotado',
            'valor_inventario'
        ));
    }

    public function clientes(Request $request)
    {
        $clientes = Cliente::withCount(['mascotas', 'ventas'])
            ->where('tenant_id', $request->tenant->id)
            ->get();

        $total_clientes = $clientes->count();
        $clientes_activos = $clientes->where('activo', true)->count();
        $clientes_con_mascotas = $clientes->filter(function($cliente) {
            return $cliente->mascotas_count > 0;
        })->count();

        // Top clientes por compras
        $top_clientes = $clientes->sortByDesc(function($cliente) {
            return $cliente->ventas->sum('total');
        })->take(10);

        return view('reportes.clientes', compact(
            'clientes',
            'total_clientes',
            'clientes_activos',
            'clientes_con_mascotas',
            'top_clientes'
        ));
    }

    public function exportarPDF(Request $request)
    {
        $tipo = $request->tipo;
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;

        switch ($tipo) {
            case 'ventas':
                $data = $this->getVentasData($fecha_inicio, $fecha_fin);
                $pdf = Pdf::loadView('reportes.pdf.ventas', $data);
                break;
            case 'citas':
                $data = $this->getCitasData($fecha_inicio, $fecha_fin);
                $pdf = Pdf::loadView('reportes.pdf.citas', $data);
                break;
            case 'inventario':
                $data = $this->getInventarioData();
                $pdf = Pdf::loadView('reportes.pdf.inventario', $data);
                break;
            default:
                abort(404);
        }

        return $pdf->download('reporte_' . $tipo . '_' . now()->format('Y-m-d') . '.pdf');
    }

    private function getVentasData($inicio, $fin)
    {
        $ventas = Venta::with(['cliente', 'detalles.producto'])
            ->where('tenant_id', request()->tenant->id)
            ->whereBetween('created_at', [$inicio, $fin])
            ->get();

        return [
            'ventas' => $ventas,
            'total_ventas' => $ventas->sum('total'),
            'total_iva' => $ventas->sum('iva'),
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'tenant' => request()->tenant
        ];
    }
}