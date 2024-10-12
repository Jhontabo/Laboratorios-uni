<?php

// config/auth.php
return [

   
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'usuarios',  // Asegúrate de que el provider sea el correcto
    ],
],

'providers' => [
    'usuarios' => [
        'driver' => 'eloquent',
        'model' => App\Models\Usuario::class,  // Modelo correcto
    ],
],


];

