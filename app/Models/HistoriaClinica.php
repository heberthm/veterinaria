<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriaClinica extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'tenant_id',
        'mascota_id',
        'veterinario_id',
        'cita_id',
        'fecha_consulta',
        'motivo_consulta',
        'anamnesis',
        'diagnostico',
        'tratamiento',          // texto libre o JSON de items
        // Signos vitales
        'temperatura',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'peso',
        'estado_corporal',
        'hidratacion',
        'examen_fisico',
        'observaciones',
        'proxima_cita_sugerida',
    ];

    protected $casts = [
        'fecha_consulta'         => 'datetime',
        'proxima_cita_sugerida'  => 'date',
        'tratamiento'            => 'array',
    ];

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'veterinario_id');
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function formulaMedica()
    {
        return $this->hasMany(FormulaMedica::class);
    }

    public function archivos()
    {
        return $this->hasMany(HistoriaClinicaArchivo::class);
    }
}
