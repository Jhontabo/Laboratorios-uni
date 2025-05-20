<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function academicProgram()
    {
        return $this->belongsTo(AcademicProgram::class);
    }
    // app/Models/Schedule.php
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_schedule')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // En tu modelo Schedule o donde tengas la relación
    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'schedule_equipment')
            ->using(ScheduleEquipment::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

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
