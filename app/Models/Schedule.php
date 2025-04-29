<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'laboratory_id',
        'user_id',
    ];

    // Ensure that date fields are cast to Carbon objects
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // 'user_id' is the key that connects to the user
    }

    // Relationship with laboratory
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id');
    }


    public function bookings()
    {
        return $this->hasMany(Booking::class, 'schedule_id');
    }

    // Accessor for the time range (optional)
    public function getTimeRangeAttribute(): string
    {
        return $this->start_at . ' - ' . $this->end_at;
    }
}
