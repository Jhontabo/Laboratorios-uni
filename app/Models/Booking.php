<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings'; // Tabla en inglés plural

    protected $fillable = [
        'user_id',
        'laboratory_id',
        'schedule_id',
        'lab_technician_id',
        'first_name',
        'last_name',
        'email',
        'rejection_reason',
        'status',
    ];

    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING = 'pending';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
