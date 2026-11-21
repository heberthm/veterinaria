<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veterinario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'veterinarios';

    protected $fillable = [
        'user_id',
        'tenant_id',
        'cedula',
        'nombre',
        'apellidos',
        'celular',
        'email',
        'especialidad',
        'activo',
    ];

    // Relación con el usuario (si tiene cuenta de acceso)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con las citas
    public function citas()
    {
        return $this->hasMany(Cita::class, 'veterinario_id');
    }
}