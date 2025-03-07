<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias'; // Indicamos la tabla correcta
    protected $primaryKey = 'id_categorias';
    protected $fillable = ['nombre_categoria']; // Campos que son asignables
}
