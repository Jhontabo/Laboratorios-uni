<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Categoria;
use App\Policies\CategoriaPolicy;
use App\Models\Laboratorio;
use App\Policies\LaboratorioPolicy;
use App\Models\Permiso;
use App\Policies\PermisoPolicy;
use App\Models\Producto;
use App\Policies\ProductoPolicy;
use App\Models\Rol;
use App\Policies\RolPolicy;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Categoria::class => CategoriaPolicy::class,
        Laboratorio::class => LaboratorioPolicy::class,
        Permiso::class => PermisoPolicy::class,
        Producto::class => ProductoPolicy::class,
        Rol::class => RolPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot()
{
    $this->registerPolicies();

    // Registrar las políticas
    Gate::define('ver cualquier categoria', [CategoriaPolicy::class, 'viewAny']);
    Gate::define('ver categoria', [CategoriaPolicy::class, 'view']);
    Gate::define('crear categoria', [CategoriaPolicy::class, 'create']);
    Gate::define('actualizar categoria', [CategoriaPolicy::class, 'update']);
    Gate::define('eliminar categoria', [CategoriaPolicy::class, 'delete']);

    Gate::define('ver cualquier horario', [HorarioPolicy::class, 'viewAny']);
    Gate::define('ver horario', [HorarioPolicy::class, 'view']);
    Gate::define('crear horario', [HorarioPolicy::class, 'create']);
    Gate::define('actualizar horario', [HorarioPolicy::class, 'update']);
    Gate::define('eliminar horario', [HorarioPolicy::class, 'delete']);

    Gate::define('ver cualquier producto', [ProductoPolicy::class, 'viewAny']);
    Gate::define('ver producto', [ProductoPolicy::class, 'view']);
    Gate::define('crear producto', [ProductoPolicy::class, 'create']);
    Gate::define('actualizar producto', [ProductoPolicy::class, 'update']);
    Gate::define('eliminar producto', [ProductoPolicy::class, 'delete']);
}
}
