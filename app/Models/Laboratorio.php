<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    use HasFactory;

    // Si la tabla tiene un nombre diferente al plural de este modelo, debes definirla:
    protected $table = 'laboratorio';
    
    protected $primaryKey = 'id_laboratorio';

    // Define los campos que se pueden llenar
    protected $fillable = ['nombre', 'ubicacion', 'capacidad'];

    // Si necesitas relaciones, por ejemplo, si un laboratorio tiene varios productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_laboratorio');
    }
}
