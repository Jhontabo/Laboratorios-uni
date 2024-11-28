## Migracion users
//

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
// public function up()
// {
// if (!Schema::hasTable('users')) {
// Schema::create('users', function (Blueprint $table) {
// $table->bigIncrements('id_usuario');
// $table->string('nombre');
// $table->string('apellido');
// $table->string('correo_electronico')->unique();
// $table->string('telefono')->nullable();
// $table->string('direccion');
// $table->enum('estado', ['activo', 'inactivo'])->default('activo');
// $table->rememberToken();
// $table->timestamps();
// });
// }
// }

// public function down()
// {
// Schema::dropIfExists('users');
// }
// };



## Laboratororio

//

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {

// public function up()
// {
// Schema::create('laboratorio', function (Blueprint $table) {
// $table->id('id_laboratorio');
// $table->string('nombre')->nullable();
// $table->string('ubicacion')->nullable();
// $table->integer('capacidad')->nullable();
// $table->timestamps();
// });
// }

// public function down()
// {
// Schema::dropIfExists('laboratorio');
// }


// };


// ## Horario

//

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
// public function up()
// {
// Schema::create('horario', function (Blueprint $table) {
// $table->id('id_horario');

// // Información básica del horario
// $table->string('title'); // Título del horario/evento
// $table->string('color')->default('#007BFF'); // Color del horario/evento
// $table->dateTime('start_at'); // Fecha y hora de inicio
// $table->dateTime('end_at'); // Fecha y hora de fin

// // Relación con la tabla laboratorio
// $table->foreignId('id_laboratorio')
// ->nullable()
// ->constrained('laboratorio', 'id_laboratorio')
// ->onDelete('cascade')
// ->onUpdate('cascade');

// // Relación con la tabla usuarios (laboratorista)
// $table->foreignId('id_usuario')
// ->constrained('users', 'id_usuario')
// ->onDelete('cascade')
// ->onUpdate('cascade');

// // Estado del horario (opcional, por ejemplo: disponible, reservado, etc.)
// $table->enum('estado', ['disponible', 'reservado', 'inactivo'])->default('disponible');

// // Campos adicionales de auditoría
// $table->timestamps(); // Campos created_at y updated_at
// });
// }

// public function down()
// {
// Schema::dropIfExists('horario');
// }
// };


// ## categoria

//

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {

// public function up()
// {
// Schema::create('categorias', function (Blueprint $table) {
// $table->id('id_categorias');
// $table->string('nombre_categoria')->nullable();
// $table->timestamps();
// });
// }

// public function down()
// {
// Schema::dropIfExists('categorias');
// }


// };


// ## reservas

//

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
// public function up()
// {
// Schema::create('reservas', function (Blueprint $table) {
// $table->id('id_reserva');

// // Relación con horario
// $table->foreignId('id_horario')
// ->constrained('horario', 'id_horario')
// ->onDelete('cascade')
// ->onUpdate('cascade');

// // Relación con usuario (estudiante)
// $table->foreignId('id_usuario')
// ->constrained('users', 'id_usuario')
// ->onDelete('cascade')
// ->onUpdate('cascade');

// $table->string('estado')->default('pendiente'); // Estado de la reserva
// $table->timestamps();
// });
// }

// public function down()
// {
// Schema::dropIfExists('reservas');
// }
// };


// ## Productos

//

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
// public function up()
// {
// // Verificar si la tabla 'productos' ya existe antes de crearla
// if (!Schema::hasTable('productos')) {
// Schema::create('productos', function (Blueprint $table) {
// $table->id('id_productos');
// $table->string('nombre')->nullable();
// $table->text('descripcion')->nullable();
// $table->integer('cantidad_disponible')->nullable();
// $table->foreignId('id_laboratorio')->constrained('laboratorio', 'id_laboratorio')->onDelete('cascade')->onUpdate('cascade');
// $table->foreignId('id_categorias')->constrained('categorias', 'id_categorias')->onDelete('cascade')->onUpdate('cascade');
// $table->string('numero_serie')->nullable();
// $table->date('fecha_adicion')->nullable();
// $table->decimal('costo_unitario', 8, 2)->nullable();
// $table->string('ubicacion')->nullable();
// $table->enum('estado', ['nuevo', 'usado', 'dañado'])->default('nuevo');
// $table->string('imagen')->nullable(); // Nueva columna para la imagen del producto
// $table->timestamps();
// });
// }
// }

// public function down()
// {
// Schema::dropIfExists('productos');
// }
// };


// ## Los modelos empizan aqui


// ## categoria

//

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;


// class Categoria extends Model
// {
// use HasFactory;

// protected $table = 'categorias'; // Indicamos la tabla correcta
// protected $primaryKey = 'id_categorias';
// protected $fillable = ['nombre_categoria']; // Campos que son asignables
// }




// ## Horario

//

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Horario extends Model
// {
// use HasFactory;

// // Nombre de la tabla
// protected $table = 'horario';

// // Clave primaria
// protected $primaryKey = 'id_horario';

// // Atributos asignables
// protected $fillable = [
// 'id_usuario',
// 'id_laboratorio',
// 'title',
// 'color',
// 'start_at',
// 'end_at',
// 'estado', // Estado del horario
// ];

// // Relación con usuario (laboratorista)
// public function usuario()
// {
// return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
// }

// // Relación con laboratorio
// public function laboratorio()
// {
// return $this->belongsTo(Laboratorio::class, 'id_laboratorio', 'id_laboratorio');
// }

// // Relación con reservas
// public function reservas()
// {
// return $this->hasMany(Reserva::class, 'id_horario', 'id_horario');
// }
// }


// ## laboratorio

//


// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Laboratorio extends Model
// {
// use HasFactory;

// // Si la tabla tiene un nombre diferente al plural de este modelo, debes definirla:
// protected $table = 'laboratorio';

// protected $primaryKey = 'id_laboratorio';

// // Define los campos que se pueden llenar
// protected $fillable = ['nombre', 'ubicacion', 'capacidad'];

// // Si necesitas relaciones, por ejemplo, si un laboratorio tiene varios productos
// public function productos()
// {
// return $this->hasMany(Producto::class, 'id_laboratorio');
// }
// }


// ## producto

//

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Producto extends Model
// {
// use HasFactory;

// protected $table = 'productos';

// protected $primaryKey = 'id_productos';

// protected $fillable = [
// 'nombre',
// 'descripcion',
// 'cantidad_disponible',
// 'id_laboratorio',
// 'id_categorias',
// 'numero_serie',
// 'fecha_adicion',
// 'costo_unitario',
// 'estado',
// 'imagen'
// ];

// // Relaciones con otros modelos
// public function laboratorio()
// {
// return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
// }

// public function categoria()
// {
// return $this->belongsTo(Categoria::class, 'id_categorias');
// }

// // Obtener la ubicación a través de la relación con laboratorio
// public function getUbicacionAttribute()
// {
// // Verifica si el laboratorio está presente antes de intentar acceder a la ubicación
// return $this->laboratorio ? $this->laboratorio->ubicacion : 'Ubicación no asignada';
// }
// }


// ## Reserva

//

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Reserva extends Model
// {
// use HasFactory;

// protected $table = 'reservas';
// protected $primaryKey = 'id_reserva';

// protected $fillable = [
// 'id_horario',
// 'id_usuario',
// 'estado',
// ];

// public function horario()
// {
// return $this->belongsTo(Horario::class, 'id_horario', 'id_horario');
// }

// public function usuario()
// {
// return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
// }
// }


## user

//

// namespace App\Models;

// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use Spatie\Permission\Traits\HasRoles;

// class User extends Authenticatable
// {
// use Notifiable, HasRoles;

// protected $table = 'users';
// protected $primaryKey = 'id_usuario';
// protected $fillable = ['nombre', 'apellido', 'correo_electronico', 'telefono', 'direccion', 'estado'];
// protected $hidden = ['remember_token'];
// public $timestamps = true;

// public function getNameAttribute()
// {
// return $this->nombre . ' ' . $this->apellido;
// }

// public function horarios()
// {
// return $this->hasMany(Horario::class, 'id_usuario', 'id_usuario');
// }

// public function reservas()
// {
// return $this->hasMany(Reserva::class, 'id_usuario', 'id_usuario');
// }
// }