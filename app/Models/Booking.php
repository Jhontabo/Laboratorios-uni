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
        'user_first_name',
        'user_last_name',
        'user_email',
        'rejection_reason',
        'status',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

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

    public function labTechnician()
    {
        return $this->belongsTo(User::class, 'lab_technician_id');
    }

    public function getReadableStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }
}

