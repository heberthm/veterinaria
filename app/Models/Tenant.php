<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre_clinica',
        'subdominio',
        'nit',
        'email_contacto',
        'telefono',
        'direccion',
        'ciudad',
        'logo',
        'plan',                 // basico | profesional | premium
        'estado',               // activo | suspendido | prueba
        'fecha_inicio_prueba',
        'fecha_fin_prueba',
        'fecha_vencimiento_plan',
    ];

    protected $casts = [
        'fecha_inicio_prueba'     => 'date',
        'fecha_fin_prueba'        => 'date',
        'fecha_vencimiento_plan'  => 'date',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function mascotas()
    {
        return $this->hasMany(Mascota::class);
    }

    public function enPrueba(): bool
    {
        return $this->estado === 'prueba'
            && $this->fecha_fin_prueba
            && $this->fecha_fin_prueba->isFuture();
    }

    public function suscripcionVencida(): bool
    {
        return $this->fecha_vencimiento_plan
            && $this->fecha_vencimiento_plan->isPast();
    }
}
