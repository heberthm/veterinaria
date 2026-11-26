<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

/**
 * Facturas = listado de CONSULTA de las ventas hechas en el POS.
 * No crea ni modifica facturas: solo lista, muestra el detalle e imprime.
 */
class FacturacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function tenantId()
    {
        return (int) (auth()->user()->tenant_id ?? 1);
    }

    /** Columna donde la venta guarda el número de factura (numero_factura o consecutivo). */
    private function columnaNumero(): ?string
    {
        $tabla = (new Venta)->getTable();
        foreach (['numero_factura', 'consecutivo'] as $col) {
            if (Schema::hasColumn($tabla, $col)) {
                return $col;
            }
        }
        return null;
    }

    /** Columna de fecha de la venta (fecha o created_at). */
    private function columnaFecha(): string
    {
        return Schema::hasColumn((new Venta)->getTable(), 'fecha') ? 'fecha' : 'created_at';
    }

    /** Número de factura tal como quedó guardado (el accesorio del modelo lo reemplaza por V-<id>). */
    private function numeroDe($venta): string
    {
        foreach (['numero_factura', 'consecutivo'] as $col) {
            $v = $venta->getRawOriginal($col);
            if (!empty($v)) {
                return (string) $v;
            }
        }
        return 'V-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Nombre del vendedor: primero el guardado en la venta (vendedor_nombre);
     * si es una venta antigua, se busca por el id (usuario_id o usurio_id) en users.
     */
    private array $cacheVendedores = [];

    private function vendedorDe($venta): ?string
    {
        if (!empty($venta->vendedor_nombre)) {
            return $venta->vendedor_nombre;
        }

        $id = $venta->usuario_id ?? $venta->usurio_id ?? null;
        if ($id) {
            if (!array_key_exists($id, $this->cacheVendedores)) {
                $this->cacheVendedores[$id] = DB::table('users')->where('id', $id)->value('name');
            }
            if ($this->cacheVendedores[$id]) {
                return $this->cacheVendedores[$id];
            }
        }

        // Último recurso: la relación usuario() del modelo
        try {
            return optional($venta->usuario)->name;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function metodoLabel($metodo): string
    {
        $metodos = [
            'efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'tarjeta_credito' => 'Tarjeta Crédito',
            'tarjeta_debito' => 'Tarjeta Débito', 'transferencia' => 'Transferencia', 'mixto' => 'Mixto',
            'nequi' => 'Nequi', 'daviplata' => 'Daviplata',
        ];
        return $metodos[$metodo] ?? ($metodo ? ucfirst(str_replace('_', ' ', $metodo)) : '—');
    }

    // ------------------------------------------------------------------
    // Pantalla
    // ------------------------------------------------------------------
    public function index()
    {
        // Acepta resources/views/facturas.blade.php o resources/views/facturas/index.blade.php
        return view()->first(['facturas', 'facturas.index']);
    }

    // ------------------------------------------------------------------
    // DataTable
    // ------------------------------------------------------------------
    public function datatable(Request $request)
    {
        $tabla    = (new Venta)->getTable();
        $colNum   = $this->columnaNumero();
        $colFecha = $this->columnaFecha();

        $query = Venta::query()
            ->with(['cliente'])
            ->where("{$tabla}.tenant_id", $this->tenantId());

        // Filtros de la pantalla
        if ($request->filled('desde')) {
            $query->whereDate("{$tabla}.{$colFecha}", '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate("{$tabla}.{$colFecha}", '<=', $request->hasta);
        }
        if ($request->filled('estado')) {
            $estado = $request->estado;
            $query->whereIn("{$tabla}.estado", $estado === 'pagada' ? ['pagada', 'completada'] : [$estado]);
        }

        $dt = DataTables::of($query)
            // Búsqueda propia: número de factura, cliente (nombre o documento) y vendedor
            ->filter(function ($q) use ($request, $colNum, $tabla) {
                $s = trim((string) data_get($request->input('search'), 'value', ''));
                if ($s === '') {
                    return;
                }
                $q->where(function ($w) use ($s, $colNum, $tabla) {
                    if ($colNum) {
                        $w->orWhere("{$tabla}.{$colNum}", 'like', "%{$s}%");
                    }
                    if (Schema::hasColumn($tabla, 'vendedor_nombre')) {
                        $w->orWhere("{$tabla}.vendedor_nombre", 'like', "%{$s}%");
                    }
                    $w->orWhereHas('cliente', function ($c) use ($s) {
                        $c->where('nombres', 'like', "%{$s}%")
                          ->orWhere('apellidos', 'like', "%{$s}%")
                          ->orWhere('numero_documento', 'like', "%{$s}%");
                    });
                });
            })
            ->addColumn('numero_factura', fn ($v) => e($this->numeroDe($v)))
            ->addColumn('cliente_nombre', function ($v) {
                return $v->cliente
                    ? e(trim($v->cliente->nombres . ' ' . $v->cliente->apellidos))
                    : 'Consumidor Final';
            })
            ->addColumn('vendedor', fn ($v) => e($this->vendedorDe($v) ?? '—'))
            ->addColumn('fecha_formato', function ($v) use ($colFecha) {
                $f = $v->{$colFecha} ?? $v->created_at;
                return $f ? \Carbon\Carbon::parse($f)->format('d/m/Y H:i') : '—';
            })
            ->addColumn('metodo_pago_label', fn ($v) => e($this->metodoLabel($v->metodo_pago ?? null)))
            ->addColumn('total_formateado', fn ($v) => '$ ' . number_format((float) $v->total, 0, ',', '.'))
            ->addColumn('estado_badge', function ($v) {
                $estado = strtolower((string) $v->estado);
                $colores = ['pendiente' => 'warning', 'completada' => 'success', 'pagada' => 'success', 'anulada' => 'danger'];
                $label = $estado === 'completada' ? 'Pagada' : ucfirst($estado ?: '—');
                return '<span class="badge badge-' . ($colores[$estado] ?? 'secondary') . '">' . e($label) . '</span>';
            })
            ->rawColumns(['estado_badge']);

        // Orden de las columnas calculadas → columna real de la tabla
        if ($colNum) {
            $dt->orderColumn('numero_factura', "{$colNum} $1");
        }
        $dt->orderColumn('fecha_formato', "{$colFecha} $1");
        $dt->orderColumn('total_formateado', 'total $1');

        return $dt->make(true);
    }

    // ------------------------------------------------------------------
    // Detalle (JSON para la ventana modal)
    // ------------------------------------------------------------------
    public function detalle($id)
    {
        $venta = Venta::with(['cliente'])
            ->where('tenant_id', $this->tenantId())
            ->findOrFail($id);

        $lineas = $this->lineas($venta);
        $subtotal = collect($lineas)->sum('subtotal');

        $estado = strtolower((string) $venta->estado);
        $fecha  = $venta->{$this->columnaFecha()} ?? $venta->created_at;

        return response()->json([
            'success' => true,
            'factura' => [
                'id'             => $venta->id,
                'numero_factura' => $this->numeroDe($venta),
                'fecha_emision'  => $fecha ? \Carbon\Carbon::parse($fecha)->format('Y-m-d H:i') : null,
                'tipo'           => 'Venta POS',
                'estado'         => $estado === 'completada' ? 'pagada' : $estado,
                'metodo_pago'    => $this->metodoLabel($venta->metodo_pago ?? null),
                'vendedor'       => $this->vendedorDe($venta),
                'observaciones'  => $venta->observacion ?? $venta->observaciones ?? null,
                'subtotal'       => (float) ($venta->subtotal ?? $subtotal),
                'descuento'      => (float) ($venta->descuento_valor ?? 0),
                'iva'            => (float) ($venta->iva_valor ?? 0),
                'total'          => (float) $venta->total,
                'saldo'          => 0,
                'cliente'        => $venta->cliente ? [
                    'nombres'          => $venta->cliente->nombres,
                    'apellidos'        => $venta->cliente->apellidos,
                    'numero_documento' => $venta->cliente->numero_documento ?? null,
                    'celular'          => $venta->cliente->celular ?? null,
                ] : null,
                'detalles'       => $lineas,
            ],
        ]);
    }

    /** Líneas de la venta, tolerando distintos nombres de tabla/columnas. */
    private function lineas(Venta $venta): array
    {
        try {
            $filas = $venta->detalles()->get();
        } catch (\Throwable $e) {
            $filas = collect();
            foreach (['ventadetalle', 'venta_detalles', 'venta_items'] as $t) {
                if (Schema::hasTable($t)) {
                    $filas = DB::table($t)->where('venta_id', $venta->id)->get();
                    break;
                }
            }
        }

        return collect($filas)->map(function ($d) {
            $d = (object) (is_object($d) && method_exists($d, 'getAttributes') ? $d->getAttributes() : (array) $d);

            $cant   = (float) ($d->cantidad ?? 1);
            $precio = (float) ($d->precio_unitario ?? $d->precio ?? 0);
            $nombre = $d->nombre_producto ?? $d->descripcion ?? null;
            if (!$nombre && !empty($d->producto_id)) {
                $nombre = optional(Producto::find($d->producto_id))->nombre;
            }

            return [
                'descripcion'     => $nombre ?: 'Producto',
                'cantidad'        => $cant,
                'precio_unitario' => $precio,
                'iva_porcentaje'  => (float) ($d->iva_porcentaje ?? 0),
                'subtotal'        => (float) ($d->subtotal ?? $d->total ?? ($cant * $precio)),
            ];
        })->values()->all();
    }

    // ------------------------------------------------------------------
    // PDF (se abre en otra pestaña para imprimir)
    // ------------------------------------------------------------------
    public function generarPDF($id)
    {
        $venta = Venta::where('tenant_id', $this->tenantId())->findOrFail($id);

        // Empresa (si el modelo Tenant la trae)
        $empresa = null;
        try {
            $t = $venta->tenant;
            $empresa = $t->nombre ?? $t->name ?? $t->razon_social ?? null;
        } catch (\Throwable $e) {
        }

        $lineas   = $this->lineas($venta);
        $subtotal = (float) ($venta->subtotal ?? collect($lineas)->sum('subtotal'));
        $descuento = (float) ($venta->descuento_valor ?? 0);
        $iva      = (float) ($venta->iva_valor ?? 0);
        $estado   = strtolower((string) $venta->estado);
        $fecha    = $venta->{$this->columnaFecha()} ?? $venta->created_at;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf-facturas', [
            'venta'     => $venta,
            'numero'    => $this->numeroDe($venta),
            'vendedor'  => $this->vendedorDe($venta) ?? '—',
            'cliente'   => $venta->cliente,
            'empresa'   => $empresa,
            'lineas'    => $lineas,
            'fecha'     => $fecha ? \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i') : '—',
            'estado'    => $estado === 'completada' ? 'pagada' : $estado,
            'metodo'    => $this->metodoLabel($venta->metodo_pago ?? null),
            'subtotal'  => $subtotal,
            'descuento' => $descuento,
            'iva'       => $iva,
            'total'     => (float) $venta->total,
        ])->setPaper('letter');
        
        return $pdf->stream('factura-' . $this->numeroDe($venta) . '.pdf');
    }
}