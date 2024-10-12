<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'cantidad_disponible',
        'id_laboratorio',
        'id_categorias',
        'numero_serie',
        'fecha_adicion',
        'costo_unitario',
        'ubicacion',
        'estado'
    ];

    // Relaciones con otros modelos
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categorias');
    }
}
