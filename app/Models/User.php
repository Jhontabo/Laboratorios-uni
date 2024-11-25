<?php

// app/Models/Usuario.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    // Nombre de la tabla
    protected $table = 'users'; // Ya tienes la tabla correcta

    // Clave primaria
    protected $primaryKey = 'id_usuario'; // Esto también ya lo has configurado

    // Atributos asignables en masa
    protected $fillable = [
        'nombre',
        'apellido',
        'correo_electronico',
        'telefono',
        'Direccion',
    ];

    // Atributos ocultos
    protected $hidden = [
        'remember_token',
    ];

    public $timestamps = true;

    // Si no usas una columna virtual, agrega este accesorio
    public function getNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}
