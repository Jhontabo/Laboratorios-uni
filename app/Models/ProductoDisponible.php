<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoDisponible extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $primaryKey = 'id_productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'cantidad_disponible',
        'id_laboratorio',
        'id_categorias',
        'id_productos',
        'numero_serie',
        'is_selected',
        'fecha_adicion',
        'fecha_adquisicion',
        'costo_unitario',
        'estado_producto',
        'estado_prestamo',
        'tipo_producto',
        'disponible_para_prestamo',
        'imagen',

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

    // Obtener la ubicación a través de la relación con laboratorio
    public function getUbicacionAttribute()
    {
        // Verifica si el laboratorio está presente antes de intentar acceder a la ubicación
        return $this->laboratorio ? $this->laboratorio->ubicacion : 'Ubicación no asignada';
    }
}
