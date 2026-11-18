<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
    'codigo',
    'nombre',
    'descripcion',
    'precio'
];

    public function stock_almacen()
    {
        return $this->hasMany(Stock_Almacen::class, 'producto_id');
    }
}
