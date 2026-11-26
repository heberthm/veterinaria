<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo genérico por clínica (especies, razas, servicios, tipos de cita,
 * métodos de pago y categorías de producto).
 */
class Catalogo extends Model
{
    protected $table = 'catalogos';

    protected $fillable = [
        'tenant_id', 'tipo', 'nombre', 'descripcion', 'valor', 'padre_id', 'orden', 'activo',
    ];

    protected $casts = [
        'valor'  => 'float',
        'activo' => 'boolean',
        'orden'  => 'integer',
    ];

    /**
     * Tipos disponibles.
     *  valor     => el tipo maneja un precio
     *  padre     => tipo del que depende (raza -> especie)
     *  sugeridos => valores que se pueden cargar con un clic
     */
    public const TIPOS = [
        'especie' => [
            'nombre' => 'Especies', 'singular' => 'Especie', 'icono' => 'fa-paw',
            'valor' => false, 'padre' => null,
            'sugeridos' => ['Perro', 'Gato', 'Ave', 'Conejo', 'Hámster', 'Reptil', 'Equino', 'Bovino'],
        ],
        'raza' => [
            'nombre' => 'Razas', 'singular' => 'Raza', 'icono' => 'fa-dog',
            'valor' => false, 'padre' => 'especie',
            'sugeridos' => [],
        ],
        'servicio' => [
            'nombre' => 'Servicios', 'singular' => 'Servicio', 'icono' => 'fa-stethoscope',
            'valor' => true, 'padre' => null,
            'sugeridos' => ['Consulta general', 'Vacunación', 'Desparasitación', 'Cirugía', 'Control', 'Baño y peluquería', 'Hospitalización'],
        ],
        'tipo_cita' => [
            'nombre' => 'Tipos de cita', 'singular' => 'Tipo de cita', 'icono' => 'fa-calendar-check',
            'valor' => false, 'padre' => null,
            'sugeridos' => ['Consulta general', 'Vacunación', 'Desparasitación', 'Cirugía', 'Control', 'Urgencia', 'Peluquería'],
        ],
        'metodo_pago' => [
            'nombre' => 'Métodos de pago', 'singular' => 'Método de pago', 'icono' => 'fa-credit-card',
            'valor' => false, 'padre' => null,
            'sugeridos' => ['Efectivo', 'Tarjeta', 'Transferencia', 'Nequi', 'Daviplata'],
        ],
        'categoria_producto' => [
            'nombre' => 'Categorías de producto', 'singular' => 'Categoría', 'icono' => 'fa-tags',
            'valor' => false, 'padre' => null,
            'sugeridos' => ['Alimentos', 'Medicamentos', 'Accesorios', 'Higiene', 'Juguetes', 'Vacunas'],
        ],
    ];

    public function padre()
    {
        return $this->belongsTo(self::class, 'padre_id');
    }

    public function scopeDelTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}