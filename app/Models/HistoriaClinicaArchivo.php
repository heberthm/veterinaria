<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriaClinicaArchivo extends Model
{
    use HasFactory;

    protected $table = 'historia_clinica_archivos';

    protected $fillable = [
        'historia_clinica_id',
        'nombre_original',
        'ruta',
        'tipo',       // imagen | pdf
        'peso_kb',
    ];

     public function getNombreAttribute() { return $this->nombre_original; }
    public function getTamanoAttribute() { return $this->peso_kb !== null ? $this->peso_kb * 1024 : null; }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }
}
