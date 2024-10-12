<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use Illuminate\Console\Command;

class CreateFilamentAdmin extends Command
{
    protected $signature = 'make:filament-admin';
    protected $description = 'Crea un nuevo usuario administrador';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->ask('¿Cuál es tu nombre, para admin?');
        $apellido = $this->ask('¿Cuál es tu apellido, para admin?');
        $email = $this->ask('¿Cuál es tu email para admin?'); 
        $telefono = $this->ask('¿Cuál es tu teléfono?'); 
        $direccion = $this->ask('¿Cuál es tu dirección?');
        
        // Creación del administrador con el campo `name` generado
        $admin = Usuario::create([
            'nombre' => $name,
            'apellido' => $apellido,
            'correo_electronico' => $email,
            'telefono' => $telefono,
            'Direccion' => $direccion,
            'name' => $name . ' ' . $apellido, // Generar el campo `name`
        ]);

        $this->info("Admin creado exitosamente");
    }
}
