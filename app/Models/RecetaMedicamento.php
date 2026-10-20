<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetaMedicamento extends Model
{
    use HasFactory;

    protected $table = 'receta_medicamentos';

    protected $fillable = ['receta_id', 'medicamento', 'dosis', 'frecuencia', 'duracion_dias', 'instrucciones'];

    public function receta() { return $this->belongsTo(Receta::class); }
}