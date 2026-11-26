<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Historial de reportes generados. Los datos de cada reporte no se guardan:
 * se calculan en vivo desde las demás tablas (ver ReporteController).
 */
class Reporte extends Model
{
    protected $table = 'reportes';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'tipo',
        'nombre',
        'fecha_desde',
        'fecha_hasta',
        'formato',
        'filtros',
        'total_registros',
    ];

    protected $casts = [
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
        'filtros'     => 'array',
    ];

    /**
     * Catálogo de reportes disponibles.
     * 'fechas' => false indica que el reporte es una "foto" actual y no usa rango de fechas.
     */
    public const TIPOS = [
        'ventas' => [
            'nombre' => 'Ventas',
            'desc'   => 'Listado de ventas del período',
            'icono'  => 'fa-shopping-cart',
            'fechas' => true,
        ],
        'ventas_vendedor' => [
            'nombre' => 'Ventas por vendedor',
            'desc'   => 'Total vendido por cada vendedor',
            'icono'  => 'fa-user-tie',
            'fechas' => true,
        ],
        'ventas_metodo' => [
            'nombre' => 'Ventas por método de pago',
            'desc'   => 'Efectivo, tarjeta, transferencia...',
            'icono'  => 'fa-credit-card',
            'fechas' => true,
        ],
        'productos_vendidos' => [
            'nombre' => 'Productos más vendidos',
            'desc'   => 'Unidades e ingresos por producto',
            'icono'  => 'fa-box-open',
            'fechas' => true,
        ],
        'compras' => [
            'nombre' => 'Compras',
            'desc'   => 'Compras a proveedores del período',
            'icono'  => 'fa-truck',
            'fechas' => true,
        ],
        'inventario' => [
            'nombre' => 'Inventario',
            'desc'   => 'Existencias, stock bajo y valor',
            'icono'  => 'fa-warehouse',
            'fechas' => false,
        ],
        'citas' => [
            'nombre' => 'Citas',
            'desc'   => 'Citas por estado en el período',
            'icono'  => 'fa-calendar-check',
            'fechas' => true,
        ],
        'pacientes' => [
            'nombre' => 'Pacientes y clientes nuevos',
            'desc'   => 'Mascotas y clientes registrados',
            'icono'  => 'fa-paw',
            'fechas' => true,
        ],
        'clientes' => [
            'nombre' => 'Top clientes',
            'desc'   => 'Clientes que más compran y sus mascotas',
            'icono'  => 'fa-users',
            'fechas' => true,
        ],
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeDelTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function getEtiquetaTipoAttribute(): string
    {
        return self::TIPOS[$this->tipo]['nombre'] ?? ucfirst(str_replace('_', ' ', (string) $this->tipo));
    }
}