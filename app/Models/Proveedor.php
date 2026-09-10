<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    // 🔥 ESPECIFICAR EL NOMBRE CORRECTO DE LA TABLA
    protected $table = 'proveedores';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'nit',
        'email',
        'telefono',
        'direccion',
        'ciudad',
        'contacto_nombre',
        'contacto_telefono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}