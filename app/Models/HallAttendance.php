<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HallAttendance extends Model
{
    public $timestamps = false;
    protected $table = 'hall_attendance';

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}
