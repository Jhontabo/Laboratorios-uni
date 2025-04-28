<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'status',
        'request_date',
        'approval_date',
        'estimated_return_date',
        'real_return_date',
        'observations',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'approval_date' => 'datetime',
        'estimated_return_date' => 'datetime',
        'real_return_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

