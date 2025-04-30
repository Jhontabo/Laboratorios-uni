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

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_RESERVED = 'reserved';
    const STATUS_REJECTED = 'rejected';


    protected $casts = [
        'status' => 'string', // Asegurar que siempre sea tratado como string
    ];

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Método para verificar si está aprobado
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    // Método para verificar si está rechazado
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

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
