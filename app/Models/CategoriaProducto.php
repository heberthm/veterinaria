<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'categorias_productos';

    protected $fillable = ['tenant_id', 'nombre', 'icono'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
