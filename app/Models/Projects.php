<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'start_date',
        'end_date',
        'users',
    ];

    protected $casts = [
        'users' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
