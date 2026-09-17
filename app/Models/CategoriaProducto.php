<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaProducto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorias_productos';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'codigo',
        'descripcion',
        'tipo',
        'activo',
        'orden',
        'imagen',
        'icono',
        'color',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    const TIPOS = [
        'producto' => 'Producto',
        'servicio' => 'Servicio',
        'paquete' => 'Paquete',
        'otro' => 'Otro',
    ];

    /**
     * Relación con el tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relación con productos
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    /**
     * Scope para categorías activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por tenant
     */
    public function scopeDelTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope para ordenar
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}