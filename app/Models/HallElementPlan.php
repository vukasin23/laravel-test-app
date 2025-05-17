<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HallElementPlan extends Model
{
    protected $fillable = [
        'hall_id', 'date', 'planned_count', 'entered_by'
    ];
    protected $casts = [
        'date' => 'date',
    ];
}
