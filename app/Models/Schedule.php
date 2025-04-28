<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class Horario extends Model
{
    use HasFactory;


    protected $table = 'horarios'; // Nombre de la tabla
    protected $primaryKey = 'id_horario'; // Clave primaria

    protected $fillable = [
        'title',
        'start_at',
        'end_at',
        'color',
        'description',
        'is_available',
        'reservation_status',
        'id_laboratorio',
        'user_id',
    ];

    // Asegúrate de que los campos de fecha se interpreten como objetos Carbon
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($horario) {
            $existe = self::where('id_laboratorio', $horario->id_laboratorio)
                ->where(function ($query) use ($horario) {
                    $query->whereBetween('start_at', [$horario->start_at, $horario->end_at])
                        ->orWhereBetween('end_at', [$horario->start_at, $horario->end_at])
                        ->orWhere(function ($query) use ($horario) {
                            $query->where('start_at', '<=', $horario->start_at)
                                ->where('end_at', '>=', $horario->end_at);
                        });
                })
                ->exists();

            if ($existe) {
                Notification::make()
                    ->title('Error')
                    ->body('Ya existe un horario en este rango de tiempo para este laboratorio.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'error' => 'Ya existe un horario en este rango de tiempo para este laboratorio.'
                ]);
            }
        });
    }

    public function laboratorista()
    {
        return $this->belongsTo(User::class, 'user_id'); // 'user_id' debe ser la clave que conecta con el usuario
    }
    // Relación con laboratorio
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }


    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_horario', 'id_horario');
    }


    // Accesor para el rango de tiempo (opcional)
    public function getTimeRangeAttribute(): string
    {
        return $this->start_at . ' - ' . $this->end_at;
    }
}
