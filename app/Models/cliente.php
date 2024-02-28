<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cliente extends Model
{
    use HasFactory;


    protected $fillable = ['cedula','user_id', 'nombre', 'direccion', 'celular', 'email', 'estado'];
}
