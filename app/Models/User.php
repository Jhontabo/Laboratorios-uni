<?php

// app/Models/Usuario.php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;

// usa esto para production 'class User extends Authenticatable implements FilamentUser'
class User extends Authenticatable
{
    use Notifiable, HasRoles;

    // contrato para que solo personas autorizadas puedan acceder al sistema
     public function canAccessPanel(Panel $panel): bool
     {
         return true;
     }


    // Nombre de la tabla en la base de datos
    protected $table = 'users';

    // Clave primaria de la tabla
    protected $primaryKey = 'id_usuario';

    // Atributos asignables en masa
    protected $fillable = [
        'nombre',
        'apellido',
        'correo_electronico',
        'telefono',
        'Direccion',
        'estado',
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
