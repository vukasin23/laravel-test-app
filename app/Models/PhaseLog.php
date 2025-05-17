<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhaseLog extends Model
{
    public $timestamps = false;

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function filledBy()
    {
        return $this->belongsTo(User::class, 'filled_by');
    }
}
