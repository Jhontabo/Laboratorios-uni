<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        // Datos generales
        'name',
        'description',
        'available_quantity',
        'laboratory_id',
        'serial_number',
        'unit_cost',
        'location',
        'acquisition_date',
        'use',
        'applies_to',
        'authorized_personnel',
        'brand',
        'model',
        'manufacturer',
        'calibration_frequency',

        // Datos específicos
        'upper_measure',
        'lower_measure',
        'associated_software',
        'user_manual',
        'dimensions',
        'weight',
        'power',
        'accessories',

        // Condiciones tolerables
        'min_temperature',
        'max_temperature',
        'min_humidity',
        'max_humidity',
        'min_voltage',
        'max_voltage',

        // Observaciones
        'observations',

        // Estado y tipo
        'product_type',
        'status',
        'available_for_loan',

        // Registro de baja
        'decommissioned_at',
        'decommissioned_by',

        // Auditoría
        'created_by',
        'updated_by',

        // Media
        'image',
    ];

    protected $casts = [
        'acquisition_date'      => 'date',
        'decommissioned_at'     => 'datetime',

        // JSON
        'applies_to'            => 'array',
        'authorized_personnel'  => 'array',
        'accessories'           => 'array',

        // Flotantes
        'unit_cost'             => 'float',
        'min_temperature'       => 'float',
        'max_temperature'       => 'float',
        'min_humidity'          => 'float',
        'max_humidity'          => 'float',
        'min_voltage'           => 'float',
        'max_voltage'           => 'float',
    ];

    public function bookings(): BelongsToMany
{
    return $this->belongsToMany(Booking::class)
        ->withPivot(['start_at', 'end_at', 'status'])
        ->withTimestamps();
}

    // Relaciones
    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'product_schedule')
            ->withPivot('quantity');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'product_id');
    }

    public function decommissionedBy()
    {
        return $this->belongsTo(User::class, 'decommissioned_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
