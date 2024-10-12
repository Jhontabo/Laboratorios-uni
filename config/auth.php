<?php

// config/auth.php
return [

   

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',  // Aquí también asegúrate de apuntar al modelo 'usuarios' si estás usando este nombre
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,  // Usa tu modelo Usuario en lugar de User
        ],
    ],

];

