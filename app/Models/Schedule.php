<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules'; // Table name
    protected $primaryKey = 'id'; // Primary key

    protected $fillable = [
        'title',
        'start_at',
        'end_at',
        'color',
        'description',
        'is_available',
        'reservation_status',
        'id_laboratory',
        'user_id',
    ];

    // Ensure that date fields are cast to Carbon objects
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($schedule) {
            $exists = self::where('id_laboratory', $schedule->id_laboratory)
                ->where(function ($query) use ($schedule) {
                    $query->whereBetween('start_at', [$schedule->start_at, $schedule->end_at])
                        ->orWhereBetween('end_at', [$schedule->start_at, $schedule->end_at])
                        ->orWhere(function ($query) use ($schedule) {
                            $query->where('start_at', '<=', $schedule->start_at)
                                ->where('end_at', '>=', $schedule->end_at);
                        });
                })
                ->exists();

            if ($exists) {
                Notification::make()
                    ->title('Error')
                    ->body('A schedule already exists in this time range for this laboratory.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'error' => 'A schedule already exists in this time range for this laboratory.'
                ]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // 'user_id' is the key that connects to the user
    }

    // Relationship with laboratory
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'id_laboratory');
    }

    public function Bookings()
    {
        return $this->hasMany(Booking::class, 'id', 'id');
    }

    // Accessor for the time range (optional)
    public function getTimeRangeAttribute(): string
    {
        return $this->start_at . ' - ' . $this->end_at;
    }
}
