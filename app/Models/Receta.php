<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'recetas';

    protected $fillable = [
        'tenant_id', 'mascota_id', 'historia_clinica_id', 'veterinario_id',
        'fecha_emision', 'indicaciones_generales', 'observaciones',
    ];

    protected $casts = ['fecha_emision' => 'date'];

    public function mascota()      { return $this->belongsTo(Mascota::class); }
    public function veterinario()  { return $this->belongsTo(User::class, 'veterinario_id'); }
    public function medicamentos() { return $this->hasMany(RecetaMedicamento::class); }

    // Fecha hasta la que está vigente: emisión + mayor duración de sus medicamentos
    public function getVigenteHastaAttribute()
    {
        return $this->fecha_emision->copy()->addDays($this->medicamentos->max('duracion_dias') ?? 0);
    }

    public function getEstadoAttribute(): string
    {
        return $this->vigente_hasta->gte(today()) ? 'activa' : 'vencida';
    }
}