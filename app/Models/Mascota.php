<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mascota extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'nombre',
        'especie',        
        'raza',
        'color',
        'fecha_nacimiento',
        'genero',
        'peso',
        'numero_chip',
        'foto',        
        'activo',
        'esterilizado',
        'alergias',
        'enfermedades_cronicas',
        'notas',
    ];

    protected static function booted()
    {
        static::saving(function ($mascota) {
            // Si hay cliente_id pero no tenant_id, heredarlo del cliente
            if ($mascota->cliente_id && !$mascota->tenant_id) {
                $cliente = Cliente::find($mascota->cliente_id);
                if ($cliente) {
                    $mascota->tenant_id = $cliente->tenant_id;
                }
            }
        });
    }

    protected $casts = [
         'fecha_nacimiento' => 'date:Y-m-d',   
        'peso' => 'float',
        'activo' => 'boolean',
        'esterilizado' => 'boolean',
    ];


    /**
     * Relación con el cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con historias clínicas
     */
    public function historiasClinicas(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class);
    }

    /**
     * Relación con citas
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    /**
     * Obtener edad de la mascota
     */
   public function getEdadAttribute()
{
    if (!$this->fecha_nacimiento) return '—';

    $hoy = now();
    $nac = \Carbon\Carbon::parse($this->fecha_nacimiento);

    if ($nac->isFuture()) return '—';

    $diff = $nac->diff($hoy);

    if ($diff->y === 0 && $diff->m === 0) {
        return $diff->d . ' día' . ($diff->d !== 1 ? 's' : '');
    }
    if ($diff->y === 0) {
        return $diff->m . ' mes' . ($diff->m !== 1 ? 'es' : '')
            . ($diff->d > 0 ? ' y ' . $diff->d . ' día' . ($diff->d !== 1 ? 's' : '') : '');
    }
    if ($diff->m === 0) {
        return $diff->y . ' año' . ($diff->y !== 1 ? 's' : '');
    }
    return $diff->y . ' año' . ($diff->y !== 1 ? 's' : '')
        . ' y ' . $diff->m . ' mes' . ($diff->m !== 1 ? 'es' : '');
}
    /**
     * Obtener URL de la foto
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nombre) . '&background=E8F0FE&color=2F6FED&size=128';
    }
}