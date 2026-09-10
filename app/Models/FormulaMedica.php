<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulaMedica extends Model
{
    use HasFactory;

    protected $table = 'formula_medica';

    protected $fillable = [
        'historia_clinica_id',
        'producto',
        'presentacion',
        'dosis',
        'cantidad',
        'frecuencia',
        'duracion',
    ];

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }
}
