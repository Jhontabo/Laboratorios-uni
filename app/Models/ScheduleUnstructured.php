<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleUnstructured extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'research_name',
        'advisor_name',
        'applicants_name',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
