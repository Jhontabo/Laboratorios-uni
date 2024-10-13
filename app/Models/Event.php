<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Especificar los campos que se pueden asignar masivamente
    protected $fillable = ['name', 'venue', 'starts_at', 'ends_at'];

    // Si 'starts_at' y 'ends_at' son campos de tipo fecha, usa este atributo

}
