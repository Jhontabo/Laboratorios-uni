<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laboratory extends Model
{
    use HasFactory;

    protected $table = 'laboratories'; // ✅ Nombre de la tabla en inglés plural

    protected $fillable = [
        'name',
        'location',
        'capacity',
        'user_id',
    ];

    // Relaciones
    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'laboratory_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'laboratory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // En el modelo Laboratory
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'laboratory_id'); // 'id_laboratory' es la clave foránea en la tabla schedules
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
