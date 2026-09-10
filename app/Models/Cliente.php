<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'tenant_id',
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellidos',
        'email',        
        'celular',
        'direccion',
        'ciudad',
        'barrio',
        'fecha_nacimiento',
        'genero',
        'activo',
        'notas',
        'foto',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * Obtener nombre completo del cliente
     */
    public function nombreCompleto()
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    /**
     * Obtener nombre completo con documento
     */
    public function nombreCompletoConDocumento()
    {
        return $this->nombreCompleto() . ' - ' . $this->numero_documento;
    }

    // ============================================
    // RELACIONES
    // ============================================

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function mascotas(): HasMany
    {
        return $this->hasMany(Mascota::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombres', 'LIKE', "%{$termino}%")
            ->orWhere('apellidos', 'LIKE', "%{$termino}%")
            ->orWhere('numero_documento', 'LIKE', "%{$termino}%")
            ->orWhere('email', 'LIKE', "%{$termino}%")
            ->orWhere('celular', 'LIKE', "%{$termino}%");
    }
}