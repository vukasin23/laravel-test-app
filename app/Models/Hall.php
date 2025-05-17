<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    public $timestamps = true;

    public function voorman()
    {
        return $this->belongsTo(User::class, 'voorman_id');
    }

    public function phases()
    {
        return $this->hasMany(Phase::class);
    }

    public function attendances()
    {
        return $this->hasMany(HallAttendance::class);
    }

    public function logs()
    {
        return $this->hasMany(PhaseLog::class);
    }
}
