<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReporteController extends Controller
{
    /** Máximo de filas por reporte (protege la memoria y el navegador). */
    private const LIMITE = 5000;

    /** Estados de venta/compra que NO cuentan como cobrados. */
    private const NO_COBRADOS = ['anulada', 'anulado', 'cancelada', 'cancelado', 'pendiente'];

    /* ===================================================================== */
    /*  Acciones públicas                                                     */
    /* ===================================================================== */

    public function index()
    {
        return view('reportes', ['tipos' => Reporte::TIPOS]);
    }

    /** JSON del reporte para mostrarlo en pantalla. */
    public function data(Request $request)
    {
        [$tipo, $desde, $hasta] = $this->parametros($request);

        try {
            $rep = $this->construir($tipo, $desde, $hasta);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'No se pudo generar el reporte' . (config('app.debug') ? ': ' . $e->getMessage() : '.'),
            ], 500);
        }

        if ($request->boolean('registrar')) {
            $this->registrar($tipo, $desde, $hasta, 'pantalla', count($rep['filas']));
        }

        return response()->json([
            'success' => true,
            'reporte' => $rep + ['tipo' => $tipo, 'desde' => $desde, 'hasta' => $hasta],
        ]);
    }

    /** Últimos reportes generados por la clínica. */
    public function historial()
    {
        if (!Schema::hasTable('reportes')) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $data = Reporte::with('usuario')
            ->delTenant($this->tenantId())
            ->latest('id')
            ->limit(15)
            ->get()
            ->map(fn ($r) => [
                'id'        => $r->id,
                'tipo'      => $r->tipo,
                'nombre'    => $r->nombre,
                'desde'     => optional($r->fecha_desde)->format('Y-m-d'),
                'hasta'     => optional($r->fecha_hasta)->format('Y-m-d'),
                'rango'     => $r->fecha_desde
                    ? $r->fecha_desde->format('d/m/Y') . ' - ' . $r->fecha_hasta->format('d/m/Y')
                    : '—',
                'formato'   => strtoupper($r->formato),
                'registros' => $r->total_registros,
                'usuario'   => optional($r->usuario)->name ?? '—',
                'fecha'     => $r->created_at->format('d/m/Y H:i'),
            ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /** PDF del reporte. */
    public function pdf(Request $request)
    {
        [$tipo, $desde, $hasta] = $this->parametros($request);
        $rep = $this->construir($tipo, $desde, $hasta);
        $this->registrar($tipo, $desde, $hasta, 'pdf', count($rep['filas']));

        $fm = $this->formatear($rep);

        return Pdf::loadView('pdf-reportes', [
            'rep'      => $rep,
            'fm'       => $fm,
            'sinFechas' => !(Reporte::TIPOS[$tipo]['fechas'] ?? true),
            'desde'    => Carbon::parse($desde)->format('d/m/Y'),
            'hasta'    => Carbon::parse($hasta)->format('d/m/Y'),
            'empresa'  => 'VetFlow',
            'generado' => now()->format('d/m/Y H:i'),
            'usuario'  => optional(auth()->user())->name,
        ])
            ->setPaper('a4', count($rep['columnas']) > 6 ? 'landscape' : 'portrait')
            ->stream("reporte-{$tipo}-{$desde}_{$hasta}.pdf");
    }

    /** CSV (se abre directo en Excel; separador ; y UTF-8 con BOM). */
    public function csv(Request $request)
    {
        [$tipo, $desde, $hasta] = $this->parametros($request);
        $rep = $this->construir($tipo, $desde, $hasta);
        $this->registrar($tipo, $desde, $hasta, 'csv', count($rep['filas']));

        $cols = $rep['columnas'];

        return response()->streamDownload(function () use ($rep, $cols) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_column($cols, 'l'), ';');

            foreach ($rep['filas'] as $fila) {
                fputcsv($out, array_map(
                    fn ($c) => $this->valorCsv($fila[$c['k']] ?? '', $c['t'] ?? 'texto'),
                    $cols
                ), ';');
            }

            if (!empty($rep['totales'])) {
                $linea = [];
                foreach ($cols as $i => $c) {
                    $linea[] = $i === 0
                        ? 'TOTAL'
                        : (isset($rep['totales'][$c['k']])
                            ? $this->valorCsv($rep['totales'][$c['k']], $c['t'] ?? 'texto')
                            : '');
                }
                fputcsv($out, $linea, ';');
            }
            fclose($out);
        }, "reporte-{$tipo}-{$desde}_{$hasta}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /* ===================================================================== */
    /*  Utilidades generales                                                  */
    /* ===================================================================== */

    private function tenantId(): int
    {
        return (int) (auth()->user()->tenant_id ?? 1);
    }

    /** Valida y normaliza tipo + rango de fechas. */
    private function parametros(Request $request): array
    {
        $tipo = (string) $request->input('tipo', 'ventas');
        abort_unless(array_key_exists($tipo, Reporte::TIPOS), 422, 'Tipo de reporte no válido.');

        $desde = $this->fecha($request->input('desde'), now()->startOfMonth());
        $hasta = $this->fecha($request->input('hasta'), now());

        if ($desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        return [$tipo, $desde, $hasta];
    }

    private function fecha($valor, Carbon $defecto): string
    {
        try {
            return $valor ? Carbon::parse($valor)->toDateString() : $defecto->toDateString();
        } catch (\Throwable $e) {
            return $defecto->toDateString();
        }
    }

    private function registrar(string $tipo, string $desde, string $hasta, string $formato, int $n): void
    {
        try {
            if (!Schema::hasTable('reportes')) {
                return;
            }
            $sinFechas = !(Reporte::TIPOS[$tipo]['fechas'] ?? true);

            Reporte::create([
                'tenant_id'       => $this->tenantId(),
                'user_id'         => auth()->id(),
                'tipo'            => $tipo,
                'nombre'          => Reporte::TIPOS[$tipo]['nombre'],
                'fecha_desde'     => $sinFechas ? null : $desde,
                'fecha_hasta'     => $sinFechas ? null : $hasta,
                'formato'         => $formato,
                'total_registros' => $n,
            ]);
        } catch (\Throwable $e) {
            report($e); // el historial nunca debe romper el reporte
        }
    }

    /** Primera columna que exista en la tabla, o null. */
    private function col(string $tabla, array $opciones): ?string
    {
        foreach ($opciones as $c) {
            if (Schema::hasColumn($tabla, $c)) {
                return $c;
            }
        }
        return null;
    }

    /** Consulta base filtrada por tenant (si la tabla tiene tenant_id). */
    private function base(string $tabla)
    {
        $q = DB::table($tabla);
        if (Schema::hasColumn($tabla, 'tenant_id')) {
            $q->where("$tabla.tenant_id", $this->tenantId());
        }
        return $q;
    }

    /** Expresión SQL con la fecha real del registro. */
    private function fechaExpr(string $tabla, array $candidatas = ['fecha', 'created_at']): string
    {
        $cols = array_values(array_filter($candidatas, fn ($c) => Schema::hasColumn($tabla, $c)));
        $cols = array_map(fn ($c) => "$tabla.$c", $cols);

        if (!$cols) {
            return 'NULL';
        }
        return count($cols) > 1 ? 'COALESCE(' . implode(',', $cols) . ')' : $cols[0];
    }

    /** Select de una columna con alias; usa un valor por defecto si no existe. */
    private function sel(string $tabla, array $opciones, string $alias, string $defecto = 'NULL')
    {
        $c = $this->col($tabla, $opciones);
        return DB::raw(($c ? "$tabla.$c" : $defecto) . " as $alias");
    }

    private function esCobrado($estado): bool
    {
        return !in_array(strtolower(trim((string) $estado)), self::NO_COBRADOS, true);
    }

    private function fmtFecha($valor): string
    {
        if (!$valor) {
            return '—';
        }
        try {
            $c = Carbon::parse($valor);
            return $c->format('H:i:s') === '00:00:00' ? $c->format('d/m/Y') : $c->format('d/m/Y H:i');
        } catch (\Throwable $e) {
            return (string) $valor;
        }
    }

    private function fmtHora($valor): string
    {
        try {
            return $valor ? Carbon::parse($valor)->format('h:i A') : '—';
        } catch (\Throwable $e) {
            return (string) $valor;
        }
    }

    /** Estructura estándar de un reporte. */
    private function armar(string $titulo, array $columnas, array $filas, array $resumen = [], array $totales = [], ?string $aviso = null): array
    {
        return compact('titulo', 'columnas', 'filas', 'resumen', 'totales', 'aviso');
    }

    private function vacio(string $titulo, string $aviso): array
    {
        return $this->armar($titulo, [], [], [], [], $aviso);
    }

    /** Suma las columnas numéricas de un conjunto de filas. */
    private function sumar(iterable $filas, array $claves): array
    {
        $t = array_fill_keys($claves, 0);
        foreach ($filas as $f) {
            foreach ($claves as $k) {
                $t[$k] += (float) ($f[$k] ?? 0);
            }
        }
        return $t;
    }

    /** Despacha el reporte según su tipo. */
    private function construir(string $tipo, string $desde, string $hasta): array
    {
        return match ($tipo) {
            'ventas'             => $this->repVentas($desde, $hasta),
            'ventas_vendedor'    => $this->repAgrupadoVentas($desde, $hasta, 'vendedor', 'Ventas por vendedor', 'Vendedor'),
            'ventas_metodo'      => $this->repAgrupadoVentas($desde, $hasta, 'metodo', 'Ventas por método de pago', 'Método de pago'),
            'productos_vendidos' => $this->repProductos($desde, $hasta),
            'compras'            => $this->repCompras($desde, $hasta),
            'inventario'         => $this->repInventario(),
            'citas'              => $this->repCitas($desde, $hasta),
            'pacientes'          => $this->repPacientes($desde, $hasta),
            'clientes'           => $this->repClientes($desde, $hasta),
        };
    }

    /* ===================================================================== */
    /*  VENTAS                                                                */
    /* ===================================================================== */

    /** Ventas del rango como colección de arrays (la usan tres reportes). */
    private function ventasColeccion(string $desde, string $hasta)
    {
        $t = 'ventas';
        if (!Schema::hasTable($t)) {
            return collect();
        }

        $fecha = $this->fechaExpr($t);
        $num   = $this->col($t, ['numero_factura', 'consecutivo']);

        $q = $this->base($t)->whereRaw("DATE($fecha) BETWEEN ? AND ?", [$desde, $hasta]);

        $sel = [
            "$t.id",
            DB::raw("$fecha as fecha"),
            DB::raw(($num
                ? "COALESCE(NULLIF($t.$num,''), CONCAT('V-',LPAD($t.id,6,'0')))"
                : "CONCAT('V-',LPAD($t.id,6,'0'))") . ' as numero'),
        ];

        // Cliente
        if ($this->col($t, ['cliente_id']) && Schema::hasTable('clientes') && Schema::hasColumn('clientes', 'nombres')) {
            $q->leftJoin('clientes as c', 'c.id', '=', "$t.cliente_id");
            $ape = Schema::hasColumn('clientes', 'apellidos') ? "COALESCE(c.apellidos,'')" : "''";
            $sel[] = DB::raw("COALESCE(NULLIF(TRIM(CONCAT(COALESCE(c.nombres,''),' ',$ape)),''),'Consumidor final') as cliente");
        } else {
            $sel[] = DB::raw("'Consumidor final' as cliente");
        }

        // Vendedor: vendedor_nombre o, si no existe, el usuario de la venta
        $partes = [];
        if (Schema::hasColumn($t, 'vendedor_nombre')) {
            $partes[] = "NULLIF($t.vendedor_nombre,'')";
        }
        $uc = $this->col($t, ['usuario_id', 'usurio_id']);
        if ($uc && Schema::hasTable('users')) {
            $q->leftJoin('users as u', 'u.id', '=', "$t.$uc");
            $partes[] = 'u.name';
        }
        $partes[] = "'Sin vendedor'";
        $sel[] = DB::raw('COALESCE(' . implode(',', $partes) . ') as vendedor');

        $sel[] = $this->sel($t, ['metodo_pago'], 'metodo', "''");
        $sel[] = $this->sel($t, ['estado'], 'estado', "'pagada'");
        $sel[] = $this->sel($t, ['subtotal'], 'subtotal', '0');
        $sel[] = $this->sel($t, ['descuento_valor', 'descuento'], 'descuento', '0');
        $sel[] = $this->sel($t, ['iva_valor', 'iva'], 'iva', '0');
        $sel[] = $this->sel($t, ['total'], 'total', '0');

        return $q->select($sel)
            ->orderByRaw("$fecha DESC")
            ->orderByDesc("$t.id")
            ->limit(self::LIMITE)
            ->get()
            ->map(fn ($v) => [
                'id'        => $v->id,
                'fecha'     => $this->fmtFecha($v->fecha),
                'numero'    => $v->numero,
                'cliente'   => $v->cliente,
                'vendedor'  => $v->vendedor,
                'metodo'    => $v->metodo !== '' && $v->metodo !== null ? ucfirst($v->metodo) : 'Sin dato',
                'estado'    => ucfirst((string) $v->estado),
                'subtotal'  => (float) $v->subtotal,
                'descuento' => (float) $v->descuento,
                'iva'       => (float) $v->iva,
                'total'     => (float) $v->total,
            ]);
    }

    private function repVentas(string $desde, string $hasta): array
    {
        $titulo = 'Reporte de ventas';
        $todas  = $this->ventasColeccion($desde, $hasta);
        $cob    = $todas->filter(fn ($v) => $this->esCobrado($v['estado']))->values();

        $total = $cob->sum('total');
        $n     = $cob->count();

        $columnas = [
            ['k' => 'fecha',     'l' => 'Fecha'],
            ['k' => 'numero',    'l' => 'N.º'],
            ['k' => 'cliente',   'l' => 'Cliente'],
            ['k' => 'vendedor',  'l' => 'Vendedor'],
            ['k' => 'metodo',    'l' => 'Pago'],
            ['k' => 'estado',    'l' => 'Estado', 't' => 'estado'],
            ['k' => 'subtotal',  'l' => 'Subtotal',  't' => 'moneda'],
            ['k' => 'descuento', 'l' => 'Descuento', 't' => 'moneda'],
            ['k' => 'iva',       'l' => 'IVA',       't' => 'moneda'],
            ['k' => 'total',     'l' => 'Total',     't' => 'moneda'],
        ];

        $resumen = [
            ['label' => 'Total vendido',    'valor' => $total,                    'tipo' => 'moneda'],
            ['label' => 'N.º de ventas',    'valor' => $n,                        'tipo' => 'numero'],
            ['label' => 'Ticket promedio',  'valor' => $n ? $total / $n : 0,      'tipo' => 'moneda'],
            ['label' => 'IVA recaudado',    'valor' => $cob->sum('iva'),          'tipo' => 'moneda'],
            ['label' => 'Anuladas / pendientes', 'valor' => $todas->count() - $n, 'tipo' => 'numero'],
        ];

        return $this->armar(
            $titulo,
            $columnas,
            $todas->all(),
            $resumen,
            $this->sumar($cob, ['subtotal', 'descuento', 'iva', 'total'])
        );
    }

    /** Ventas agrupadas por 'vendedor' o 'metodo'. */
    private function repAgrupadoVentas(string $desde, string $hasta, string $campo, string $titulo, string $etiqueta): array
    {
        $cob = $this->ventasColeccion($desde, $hasta)
            ->filter(fn ($v) => $this->esCobrado($v['estado']));

        $total = $cob->sum('total');

        $filas = $cob->groupBy($campo)->map(function ($g, $nombre) use ($total, $campo) {
            $suma = $g->sum('total');
            return [
                $campo     => $nombre,
                'ventas'   => $g->count(),
                'total'    => $suma,
                'promedio' => $g->count() ? $suma / $g->count() : 0,
                'part'     => $total ? ($suma / $total) * 100 : 0,
            ];
        })->sortByDesc('total')->values()->all();

        $columnas = [
            ['k' => $campo,      'l' => $etiqueta],
            ['k' => 'ventas',    'l' => 'N.º de ventas',   't' => 'numero'],
            ['k' => 'total',     'l' => 'Total vendido',   't' => 'moneda'],
            ['k' => 'promedio',  'l' => 'Ticket promedio', 't' => 'moneda'],
            ['k' => 'part',      'l' => '% del total',     't' => 'porcentaje'],
        ];

        $resumen = [
            ['label' => 'Total vendido', 'valor' => $total,       'tipo' => 'moneda'],
            ['label' => 'N.º de ventas', 'valor' => $cob->count(), 'tipo' => 'numero'],
            ['label' => $campo === 'vendedor' ? 'Vendedores' : 'Métodos usados', 'valor' => count($filas), 'tipo' => 'numero'],
        ];

        return $this->armar($titulo, $columnas, $filas, $resumen, $this->sumar($filas, ['ventas', 'total']));
    }

    /** Tabla de detalle de ventas (el nombre varía entre instalaciones). */
    private function tablaDetalle(): ?string
    {
        foreach (['ventadetalle', 'venta_detalles', 'venta_items', 'venta_detalle'] as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'venta_id')) {
                return $t;
            }
        }
        return null;
    }

    private function repProductos(string $desde, string $hasta): array
    {
        $titulo = 'Productos más vendidos';
        $v      = 'ventas';
        $det    = $this->tablaDetalle();

        if (!$det || !Schema::hasTable($v)) {
            return $this->vacio($titulo, 'No se encontró la tabla de detalle de ventas.');
        }

        $cant = $this->col($det, ['cantidad']);
        if (!$cant) {
            return $this->vacio($titulo, "La tabla $det no tiene columna de cantidad.");
        }

        $pre = $this->col($det, ['precio_unitario', 'precio', 'precio_venta']);
        $sub = $this->col($det, ['subtotal', 'total']);
        $importe = $sub ? "$det.$sub" : ($pre ? "($det.$cant * $det.$pre)" : '0');

        $q = DB::table($det)->join($v, "$v.id", '=', "$det.venta_id");

        if (Schema::hasColumn($v, 'tenant_id')) {
            $q->where("$v.tenant_id", $this->tenantId());
        }
        if (Schema::hasColumn($v, 'estado')) {
            $q->whereRaw("LOWER(COALESCE($v.estado,'')) NOT IN ('" . implode("','", self::NO_COBRADOS) . "')");
        }
        $fecha = $this->fechaExpr($v);
        $q->whereRaw("DATE($fecha) BETWEEN ? AND ?", [$desde, $hasta]);

        // Nombre del producto: texto del detalle o catálogo de productos
        $nombres = [];
        foreach (['nombre', 'descripcion', 'producto'] as $c) {
            if (Schema::hasColumn($det, $c)) {
                $nombres[] = "NULLIF($det.$c,'')";
            }
        }
        if (Schema::hasTable('productos') && Schema::hasColumn($det, 'producto_id') && Schema::hasColumn('productos', 'nombre')) {
            $q->leftJoin('productos as p', 'p.id', '=', "$det.producto_id");
            $nombres[] = 'p.nombre';
        }
        $nombres[] = "'Sin nombre'";

        $filas = $q->select([
                DB::raw('COALESCE(' . implode(',', $nombres) . ') as producto'),
                DB::raw("$det.$cant as cantidad"),
                DB::raw("$importe as importe"),
            ])
            ->limit(self::LIMITE * 4)
            ->get()
            ->groupBy('producto')
            ->map(fn ($g, $nombre) => [
                'producto' => $nombre,
                'cantidad' => (float) $g->sum('cantidad'),
                'importe'  => (float) $g->sum('importe'),
            ])
            ->sortByDesc('cantidad')
            ->values();

        $totalImporte = $filas->sum('importe');
        $filas = $filas->map(fn ($f) => $f + ['part' => $totalImporte ? ($f['importe'] / $totalImporte) * 100 : 0])->all();

        $columnas = [
            ['k' => 'producto', 'l' => 'Producto'],
            ['k' => 'cantidad', 'l' => 'Unidades vendidas', 't' => 'numero'],
            ['k' => 'importe',  'l' => 'Ingresos',          't' => 'moneda'],
            ['k' => 'part',     'l' => '% de ingresos',     't' => 'porcentaje'],
        ];

        $resumen = [
            ['label' => 'Productos distintos', 'valor' => count($filas),                         'tipo' => 'numero'],
            ['label' => 'Unidades vendidas',   'valor' => array_sum(array_column($filas, 'cantidad')), 'tipo' => 'numero'],
            ['label' => 'Ingresos',            'valor' => $totalImporte,                         'tipo' => 'moneda'],
        ];

        return $this->armar($titulo, $columnas, $filas, $resumen, $this->sumar($filas, ['cantidad', 'importe']));
    }

    /* ===================================================================== */
    /*  COMPRAS                                                               */
    /* ===================================================================== */

    private function repCompras(string $desde, string $hasta): array
    {
        $titulo = 'Reporte de compras';
        $t = 'compras';

        if (!Schema::hasTable($t)) {
            return $this->vacio($titulo, 'No existe la tabla de compras.');
        }

        $fecha = $this->fechaExpr($t, ['fecha', 'fecha_compra', 'created_at']);
        $q = $this->base($t)->whereRaw("DATE($fecha) BETWEEN ? AND ?", [$desde, $hasta]);

        $sel = [
            "$t.id",
            DB::raw("$fecha as fecha"),
            $this->sel($t, ['numero_factura', 'numero_factura_proveedor', 'consecutivo'], 'numero', "CONCAT('C-',LPAD($t.id,6,'0'))"),
            $this->sel($t, ['estado'], 'estado', "''"),
            $this->sel($t, ['total'], 'total', '0'),
        ];

        // Proveedor
        $nombreProv = Schema::hasTable('proveedores')
            ? $this->col('proveedores', ['nombre', 'razon_social', 'nombre_comercial', 'empresa'])
            : null;

        if ($this->col($t, ['proveedor_id']) && $nombreProv) {
            $q->leftJoin('proveedores as pr', 'pr.id', '=', "$t.proveedor_id");
            $sel[] = DB::raw("COALESCE(pr.$nombreProv,'—') as proveedor");
        } elseif (Schema::hasColumn($t, 'proveedor')) {
            $sel[] = DB::raw("COALESCE($t.proveedor,'—') as proveedor");
        } else {
            $sel[] = DB::raw("'—' as proveedor");
        }

        $todas = $q->select($sel)
            ->orderByRaw("$fecha DESC")
            ->orderByDesc("$t.id")
            ->limit(self::LIMITE)
            ->get()
            ->map(fn ($c) => [
                'fecha'     => $this->fmtFecha($c->fecha),
                'numero'    => $c->numero,
                'proveedor' => $c->proveedor,
                'estado'    => $c->estado !== '' && $c->estado !== null ? ucfirst($c->estado) : '—',
                'total'     => (float) $c->total,
            ]);

        $validas = $todas->filter(fn ($c) => $this->esCobrado($c['estado']));
        $total   = $validas->sum('total');
        $n       = $validas->count();

        $columnas = [
            ['k' => 'fecha',     'l' => 'Fecha'],
            ['k' => 'numero',    'l' => 'N.º factura'],
            ['k' => 'proveedor', 'l' => 'Proveedor'],
            ['k' => 'estado',    'l' => 'Estado', 't' => 'estado'],
            ['k' => 'total',     'l' => 'Total',  't' => 'moneda'],
        ];

        $resumen = [
            ['label' => 'Total comprado',  'valor' => $total,                'tipo' => 'moneda'],
            ['label' => 'N.º de compras',  'valor' => $n,                    'tipo' => 'numero'],
            ['label' => 'Compra promedio', 'valor' => $n ? $total / $n : 0,  'tipo' => 'moneda'],
        ];

        return $this->armar($titulo, $columnas, $todas->all(), $resumen, $this->sumar($validas, ['total']));
    }

    /* ===================================================================== */
    /*  INVENTARIO                                                            */
    /* ===================================================================== */

    private function repInventario(): array
    {
        $titulo = 'Reporte de inventario';
        $t = 'productos';

        if (!Schema::hasTable($t)) {
            return $this->vacio($titulo, 'No existe la tabla de productos.');
        }

        $q = $this->base($t);
        if (Schema::hasColumn($t, 'activo')) {
            $q->where("$t.activo", true);
        }

        $codigo = $this->col($t, ['codigo', 'sku', 'codigo_barras']);
        $min    = $this->col($t, ['stock_minimo', 'stock_min', 'minimo']);
        $costo  = $this->col($t, ['precio_compra', 'costo', 'precio_costo']);
        $venta  = $this->col($t, ['precio_venta', 'precio']);

        $sel = [
            $this->sel($t, ['nombre'], 'nombre', "''"),
            $this->sel($t, ['stock'], 'stock', '0'),
            $this->sel($t, [$min ?? '__x'], 'minimo', 'NULL'),
            $this->sel($t, [$costo ?? '__x'], 'costo', '0'),
            $this->sel($t, [$venta ?? '__x'], 'precio', '0'),
        ];
        if ($codigo) {
            $sel[] = DB::raw("$t.$codigo as codigo");
        }

        $filas = $q->select($sel)
            ->orderBy("$t." . ($this->col($t, ['nombre']) ?? 'id'))
            ->limit(self::LIMITE)
            ->get()
            ->map(function ($p) use ($codigo) {
                $stock  = (float) $p->stock;
                $minimo = $p->minimo !== null ? (float) $p->minimo : null;
                $unit   = (float) ($p->costo ?: $p->precio);

                $estado = $stock <= 0 ? 'Agotado' : (($minimo !== null && $stock <= $minimo) ? 'Bajo' : 'Normal');

                return [
                    'codigo'  => $codigo ? ($p->codigo ?? '—') : '—',
                    'nombre'  => $p->nombre,
                    'stock'   => $stock,
                    'minimo'  => $minimo ?? 0,
                    'costo'   => (float) $p->costo,
                    'precio'  => (float) $p->precio,
                    'valor'   => $stock * $unit,
                    'estado'  => $estado,
                ];
            });

        $columnas = [];
        if ($codigo) {
            $columnas[] = ['k' => 'codigo', 'l' => 'Código'];
        }
        $columnas[] = ['k' => 'nombre', 'l' => 'Producto'];
        $columnas[] = ['k' => 'stock',  'l' => 'Stock', 't' => 'numero'];
        if ($min) {
            $columnas[] = ['k' => 'minimo', 'l' => 'Mínimo', 't' => 'numero'];
        }
        if ($costo) {
            $columnas[] = ['k' => 'costo', 'l' => 'Costo', 't' => 'moneda'];
        }
        if ($venta) {
            $columnas[] = ['k' => 'precio', 'l' => 'Precio venta', 't' => 'moneda'];
        }
        $columnas[] = ['k' => 'valor',  'l' => 'Valor en inventario', 't' => 'moneda'];
        $columnas[] = ['k' => 'estado', 'l' => 'Estado', 't' => 'estado'];

        $resumen = [
            ['label' => 'Productos activos',    'valor' => $filas->count(),                                   'tipo' => 'numero'],
            ['label' => 'Agotados',             'valor' => $filas->where('estado', 'Agotado')->count(),       'tipo' => 'numero'],
            ['label' => 'Stock bajo',           'valor' => $filas->where('estado', 'Bajo')->count(),          'tipo' => 'numero'],
            ['label' => 'Valor del inventario', 'valor' => $filas->sum('valor'),                              'tipo' => 'moneda'],
        ];

        return $this->armar($titulo, $columnas, $filas->all(), $resumen, $this->sumar($filas, ['valor']));
    }

    /* ===================================================================== */
    /*  CITAS                                                                 */
    /* ===================================================================== */

    private function repCitas(string $desde, string $hasta): array
    {
        $titulo = 'Reporte de citas';
        $t = 'citas';

        if (!Schema::hasTable($t)) {
            return $this->vacio($titulo, 'No existe la tabla de citas.');
        }

        $fecha = $this->fechaExpr($t, ['fecha', 'created_at']);
        $q = $this->base($t)->whereRaw("DATE($fecha) BETWEEN ? AND ?", [$desde, $hasta]);

        $sel = [
            "$t.id",
            DB::raw("$fecha as fecha"),
            $this->sel($t, ['hora_inicio', 'hora'], 'hora'),
            $this->sel($t, ['estado'], 'estado', "''"),
            $this->sel($t, ['motivo', 'tipo', 'tipo_cita', 'servicio'], 'motivo', "''"),
        ];

        if ($this->col($t, ['mascota_id']) && Schema::hasTable('mascotas')) {
            $q->leftJoin('mascotas as m', 'm.id', '=', "$t.mascota_id");
            $sel[] = DB::raw("COALESCE(m.nombre,'—') as mascota");

            if (Schema::hasTable('clientes') && Schema::hasColumn('mascotas', 'cliente_id') && Schema::hasColumn('clientes', 'nombres')) {
                $q->leftJoin('clientes as c', 'c.id', '=', 'm.cliente_id');
                $ape = Schema::hasColumn('clientes', 'apellidos') ? "COALESCE(c.apellidos,'')" : "''";
                $sel[] = DB::raw("COALESCE(NULLIF(TRIM(CONCAT(COALESCE(c.nombres,''),' ',$ape)),''),'—') as cliente");
            } else {
                $sel[] = DB::raw("'—' as cliente");
            }
        } else {
            $sel[] = DB::raw("'—' as mascota");
            $sel[] = DB::raw("'—' as cliente");
        }

        $filas = $q->select($sel)
            ->orderByRaw("$fecha ASC")
            ->orderBy($this->col($t, ['hora_inicio', 'hora']) ? "$t." . $this->col($t, ['hora_inicio', 'hora']) : "$t.id")
            ->limit(self::LIMITE)
            ->get()
            ->map(fn ($c) => [
                'fecha'   => $this->fmtFecha($c->fecha),
                'hora'    => $this->fmtHora($c->hora),
                'mascota' => $c->mascota,
                'cliente' => $c->cliente,
                'motivo'  => $c->motivo !== '' && $c->motivo !== null ? $c->motivo : '—',
                'estado'  => $c->estado !== '' && $c->estado !== null ? ucfirst(str_replace('_', ' ', $c->estado)) : '—',
                '_estado' => strtolower((string) $c->estado),
            ]);

        $por = fn (string $e) => $filas->where('_estado', $e)->count();

        $resumen = [
            ['label' => 'Total de citas', 'valor' => $filas->count(),     'tipo' => 'numero'],
            ['label' => 'Confirmadas',    'valor' => $por('confirmada'),  'tipo' => 'numero'],
            ['label' => 'Pendientes',     'valor' => $por('pendiente'),   'tipo' => 'numero'],
            ['label' => 'En atención',    'valor' => $por('en_atencion'), 'tipo' => 'numero'],
            ['label' => 'Canceladas',     'valor' => $por('cancelada'),   'tipo' => 'numero'],
        ];

        $columnas = [
            ['k' => 'fecha',   'l' => 'Fecha'],
            ['k' => 'hora',    'l' => 'Hora'],
            ['k' => 'mascota', 'l' => 'Mascota'],
            ['k' => 'cliente', 'l' => 'Cliente'],
            ['k' => 'motivo',  'l' => 'Motivo'],
            ['k' => 'estado',  'l' => 'Estado', 't' => 'estado'],
        ];

        return $this->armar($titulo, $columnas, $filas->map(fn ($f) => collect($f)->except('_estado')->all())->all(), $resumen);
    }

    /* ===================================================================== */
    /*  PACIENTES Y CLIENTES NUEVOS                                           */
    /* ===================================================================== */

    private function repPacientes(string $desde, string $hasta): array
    {
        $titulo = 'Pacientes y clientes nuevos';
        $t = 'mascotas';

        if (!Schema::hasTable($t)) {
            return $this->vacio($titulo, 'No existe la tabla de mascotas.');
        }

        $q = $this->base($t)->whereRaw("DATE($t.created_at) BETWEEN ? AND ?", [$desde, $hasta]);

        $sel = [
            DB::raw("$t.created_at as fecha"),
            $this->sel($t, ['nombre'], 'nombre', "''"),
        ];
        $tieneEspecie = Schema::hasColumn($t, 'especie');
        $tieneRaza    = Schema::hasColumn($t, 'raza');
        if ($tieneEspecie) {
            $sel[] = DB::raw("$t.especie as especie");
        }
        if ($tieneRaza) {
            $sel[] = DB::raw("$t.raza as raza");
        }

        if (Schema::hasColumn($t, 'cliente_id') && Schema::hasTable('clientes') && Schema::hasColumn('clientes', 'nombres')) {
            $q->leftJoin('clientes as c', 'c.id', '=', "$t.cliente_id");
            $ape = Schema::hasColumn('clientes', 'apellidos') ? "COALESCE(c.apellidos,'')" : "''";
            $sel[] = DB::raw("COALESCE(NULLIF(TRIM(CONCAT(COALESCE(c.nombres,''),' ',$ape)),''),'—') as cliente");
        } else {
            $sel[] = DB::raw("'—' as cliente");
        }

        $filas = $q->select($sel)
            ->orderByDesc("$t.created_at")
            ->limit(self::LIMITE)
            ->get()
            ->map(fn ($m) => [
                'fecha'   => $this->fmtFecha($m->fecha),
                'nombre'  => $m->nombre,
                'especie' => $m->especie ?? '—',
                'raza'    => $m->raza ?? '—',
                'cliente' => $m->cliente,
            ]);

        $clientesNuevos = Schema::hasTable('clientes')
            ? $this->base('clientes')->whereRaw('DATE(clientes.created_at) BETWEEN ? AND ?', [$desde, $hasta])->count()
            : 0;

        $resumen = [
            ['label' => 'Mascotas nuevas',     'valor' => $filas->count(),              'tipo' => 'numero'],
            ['label' => 'Clientes nuevos',     'valor' => $clientesNuevos,              'tipo' => 'numero'],
            ['label' => 'Mascotas registradas', 'valor' => $this->base($t)->count(),    'tipo' => 'numero'],
        ];

        $columnas = [
            ['k' => 'fecha',  'l' => 'Registrada'],
            ['k' => 'nombre', 'l' => 'Mascota'],
        ];
        if ($tieneEspecie) {
            $columnas[] = ['k' => 'especie', 'l' => 'Especie'];
        }
        if ($tieneRaza) {
            $columnas[] = ['k' => 'raza', 'l' => 'Raza'];
        }
        $columnas[] = ['k' => 'cliente', 'l' => 'Propietario'];

        return $this->armar($titulo, $columnas, $filas->all(), $resumen);
    }

    /* ===================================================================== */
    /*  TOP CLIENTES                                                          */
    /* ===================================================================== */

    private function repClientes(string $desde, string $hasta): array
    {
        $titulo = 'Top clientes';
        $v = 'ventas';

        if (!Schema::hasTable($v) || !Schema::hasTable('clientes') || !Schema::hasColumn($v, 'cliente_id')) {
            return $this->vacio($titulo, 'Faltan las tablas ventas/clientes o la columna cliente_id.');
        }

        $fecha = $this->fechaExpr($v);
        $q = $this->base($v)
            ->whereNotNull("$v.cliente_id")
            ->whereRaw("DATE($fecha) BETWEEN ? AND ?", [$desde, $hasta]);

        if (Schema::hasColumn($v, 'estado')) {
            $q->whereRaw("LOWER(COALESCE($v.estado,'')) NOT IN ('" . implode("','", self::NO_COBRADOS) . "')");
        }

        $compras = $q->select([
                "$v.cliente_id",
                $this->sel($v, ['total'], 'total', '0'),
                DB::raw("$fecha as fecha"),
            ])
            ->limit(self::LIMITE * 4)
            ->get()
            ->groupBy('cliente_id');

        $ids = $compras->keys()->all();

        $clientes = $ids
            ? $this->base('clientes')->whereIn('clientes.id', $ids)->get()->keyBy('id')
            : collect();

        $mascotas = collect();
        $conMascotas = 0;
        if (Schema::hasTable('mascotas') && Schema::hasColumn('mascotas', 'cliente_id')) {
            if ($ids) {
                $mascotas = $this->base('mascotas')
                    ->whereIn('cliente_id', $ids)
                    ->selectRaw('cliente_id, COUNT(*) as n')
                    ->groupBy('cliente_id')
                    ->pluck('n', 'cliente_id');
            }
            $conMascotas = $this->base('mascotas')->distinct()->count('cliente_id');
        }

        $filas = $compras->map(function ($g, $id) use ($clientes, $mascotas) {
            $c      = $clientes[$id] ?? null;
            $nombre = $c ? trim(($c->nombres ?? '') . ' ' . ($c->apellidos ?? '')) : '';
            $suma   = (float) $g->sum('total');

            return [
                'cliente'   => $nombre !== '' ? $nombre : 'Cliente #' . $id,
                'documento' => $c->numero_documento ?? '—',
                'mascotas'  => (int) ($mascotas[$id] ?? 0),
                'compras'   => $g->count(),
                'total'     => $suma,
                'promedio'  => $g->count() ? $suma / $g->count() : 0,
                'ultima'    => $this->fmtFecha($g->max('fecha')),
            ];
        })->sortByDesc('total')->values()->all();

        $columnas = [
            ['k' => 'cliente',   'l' => 'Cliente'],
            ['k' => 'documento', 'l' => 'Documento'],
            ['k' => 'mascotas',  'l' => 'Mascotas',        't' => 'numero'],
            ['k' => 'compras',   'l' => 'N.º de compras',  't' => 'numero'],
            ['k' => 'total',     'l' => 'Total comprado',  't' => 'moneda'],
            ['k' => 'promedio',  'l' => 'Compra promedio', 't' => 'moneda'],
            ['k' => 'ultima',    'l' => 'Última compra'],
        ];

        $resumen = [
            ['label' => 'Clientes con compras',   'valor' => count($filas),                              'tipo' => 'numero'],
            ['label' => 'Total comprado',         'valor' => array_sum(array_column($filas, 'total')),   'tipo' => 'moneda'],
            ['label' => 'Clientes registrados',   'valor' => $this->base('clientes')->count(),           'tipo' => 'numero'],
            ['label' => 'Clientes con mascotas',  'valor' => $conMascotas,                               'tipo' => 'numero'],
        ];

        return $this->armar($titulo, $columnas, $filas, $resumen, $this->sumar($filas, ['compras', 'total']));
    }

    /* ===================================================================== */
    /*  Formato para PDF y CSV                                                */
    /* ===================================================================== */

    private function fmt($valor, string $tipo): string
    {
        switch ($tipo) {
            case 'moneda':
                return '$ ' . number_format((float) $valor, 0, ',', '.');
            case 'numero':
                $v = (float) $valor;
                return number_format($v, floor($v) == $v ? 0 : 2, ',', '.');
            case 'porcentaje':
                return number_format((float) $valor, 1, ',', '.') . '%';
            default:
                return (string) $valor;
        }
    }

    /** Valores ya formateados como texto para la vista PDF. */
    private function formatear(array $rep): array
    {
        $tipos = [];
        foreach ($rep['columnas'] as $c) {
            $tipos[$c['k']] = $c['t'] ?? 'texto';
        }

        $filas = array_map(function ($f) use ($rep) {
            $o = [];
            foreach ($rep['columnas'] as $c) {
                $o[$c['k']] = $this->fmt($f[$c['k']] ?? '', $c['t'] ?? 'texto');
            }
            return $o;
        }, $rep['filas']);

        $totales = [];
        foreach ($rep['totales'] as $k => $v) {
            $totales[$k] = $this->fmt($v, $tipos[$k] ?? 'numero');
        }

        $resumen = array_map(fn ($r) => [
            'label' => $r['label'],
            'valor' => $this->fmt($r['valor'], $r['tipo']),
        ], $rep['resumen']);

        return compact('filas', 'totales', 'resumen');
    }

    /** Valor para CSV: números sin separador de miles y con coma decimal. */
    private function valorCsv($valor, string $tipo): string
    {
        return match ($tipo) {
            'moneda'     => (string) round((float) $valor),
            'numero'     => number_format((float) $valor, floor((float) $valor) == (float) $valor ? 0 : 2, ',', ''),
            'porcentaje' => number_format((float) $valor, 1, ',', ''),
            default      => (string) $valor,
        };
    }
}