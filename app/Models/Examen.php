<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'examenes';   // sin esto Laravel buscaría "examens"

    protected $fillable = [
        'tenant_id', 'mascota_id', 'historia_clinica_id', 'veterinario_id',
        'categoria', 'nombre', 'fecha', 'resultado', 'observaciones',
        'archivo_ruta', 'archivo_nombre',
    ];

    protected $casts = ['fecha' => 'date'];

    public function mascota()      { return $this->belongsTo(Mascota::class); }
    public function veterinario()  { return $this->belongsTo(User::class, 'veterinario_id'); }
}