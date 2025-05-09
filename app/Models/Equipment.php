<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Equipment extends Model

{

    protected $table = 'equipments';
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_equipment')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
