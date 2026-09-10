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
        'foto',        // 🔥 AGREGADO
        'activo',
        'esterilizado',
        'alergias',
        'enfermedades_cronicas',
        'notas',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
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
        if (!$this->fecha_nacimiento) {
            return 'No registrada';
        }
        return $this->fecha_nacimiento->age . ' años';
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