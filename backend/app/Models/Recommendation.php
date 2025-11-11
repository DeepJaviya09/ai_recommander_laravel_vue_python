<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recommended_products',
    ];

    protected $casts = [
        'recommended_products' => 'array',
    ];
}
