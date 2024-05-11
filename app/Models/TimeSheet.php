<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheet extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_name', 'date', 'time', 'project_id', 'user_id',
    ];

    /**
     * Get the project that owns the time sheet.
     */
    public function project()
    {
        return $this->belongsTo(Projects::class);
    }

    /**
     * Get the user that owns the time sheet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
