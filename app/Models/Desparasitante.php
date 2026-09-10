<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desparasitante extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'nombre',
        'laboratorio',
        'lote',
        'descripcion',
        'tipo',
        'principio_activo',
        'precio',
        'stock',
        'stock_minimo',
        'fecha_caducidad',
        'via_aplicacion',
        'activo',
    ];

    protected $casts = [
        'fecha_caducidad' => 'date',
        'activo' => 'boolean',
        'precio' => 'decimal:2',
    ];

    const TIPOS = [
        'interno' => 'Interno',
        'externo' => 'Externo',
        'ambos' => 'Ambos',
    ];

    const VIAS_APLICACION = [
        'oral' => 'Oral',
        'topico' => 'Tópico',
        'inyectable' => 'Inyectable',
        'spray' => 'Spray',
        'pipeta' => 'Pipeta',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function aplicaciones(): HasMany
    {
        return $this->hasMany(DesparasitacionAplicacion::class);
    }

    public function estaVencida(): bool
    {
        return $this->fecha_caducidad && $this->fecha_caducidad->isPast();
    }

    public function necesitaStock(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}