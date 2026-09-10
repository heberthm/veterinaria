<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'nombre', 'direccion', 'principal', 'activa'];

    protected $casts = [
        'principal' => 'boolean',
        'activa'    => 'boolean',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
