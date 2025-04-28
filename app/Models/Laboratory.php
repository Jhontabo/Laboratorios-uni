<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function products()
    {
        return $this->hasMany(Product::class, 'laboratory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

