<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id_categorias
 * @property string|null $nombre_categoria
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereIdCategorias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereNombreCategoria($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereUpdatedAt($value)
 */
	class Categoria extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id_horario
 * @property string $title
 * @property \Illuminate\Support\Carbon $start_at
 * @property \Illuminate\Support\Carbon $end_at
 * @property string|null $color
 * @property string|null $description
 * @property int $is_available
 * @property int|null $id_laboratorio
 * @property int|null $id_usuario
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $time_range
 * @property-read \App\Models\Laboratorio|null $laboratorio
 * @property-read \App\Models\User|null $laboratorista
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reserva> $reservas
 * @property-read int|null $reservas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereIdHorario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereIdLaboratorio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Horario whereUpdatedAt($value)
 */
	class Horario extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id_laboratorio
 * @property string|null $nombre
 * @property string|null $ubicacion
 * @property int|null $capacidad
 * @property int|null $id_usuario
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $laboratorista
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Producto> $productos
 * @property-read int|null $productos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio whereCapacidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio whereIdLaboratorio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio whereUbicacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laboratorio whereUpdatedAt($value)
 */
	class Laboratorio extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso withoutRole($roles, $guard = null)
 */
	class Permiso extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id_productos
 * @property string|null $nombre
 * @property string|null $descripcion
 * @property int|null $cantidad_disponible
 * @property int $id_laboratorio
 * @property int $id_categorias
 * @property string|null $numero_serie
 * @property string|null $fecha_adicion
 * @property string|null $fecha_adquisicion
 * @property string|null $costo_unitario
 * @property string|null $ubicacion
 * @property string $tipo_producto
 * @property string $estado
 * @property string|null $imagen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Categoria $categoria
 * @property-read \App\Models\Laboratorio $laboratorio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereCantidadDisponible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereCostoUnitario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereFechaAdicion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereFechaAdquisicion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereIdCategorias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereIdLaboratorio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereIdProductos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereImagen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereNumeroSerie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereTipoProducto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereUbicacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producto whereUpdatedAt($value)
 */
	class Producto extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id_reserva
 * @property string|null $nombre_usuario Nombre del usuario que realizó la reserva
 * @property string|null $apellido_usuario Apellido del usuario que realizó la reserva
 * @property string|null $correo_usuario Correo del usuario que realizó la reserva
 * @property string|null $razon_rechazo Razón del rechazo de la reserva
 * @property int $id_horario
 * @property int|null $id_usuario
 * @property string $estado Estado de la reserva
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $estado_legible
 * @property-read \App\Models\Horario $horario
 * @property-read \App\Models\Laboratorio|null $laboratorio
 * @property-read \App\Models\User|null $laboratorista
 * @property-read \App\Models\User|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereApellidoUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereCorreoUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereIdHorario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereIdReserva($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereNombreUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereRazonRechazo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereUpdatedAt($value)
 */
	class Reserva extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol withoutPermission($permissions)
 */
	class Rol extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id_usuario
 * @property string $name
 * @property string $apellido
 * @property string $email
 * @property string|null $telefono
 * @property string $direccion
 * @property string $estado
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $theme
 * @property string|null $theme_color
 * @property array|null $custom_fields
 * @property string|null $avatar_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCustomFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdUsuario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereThemeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\HasAvatar, \Filament\Models\Contracts\FilamentUser {}
}

