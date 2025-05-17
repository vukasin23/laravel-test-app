<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    public $timestamps = true;

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\PhaseItem::class);
    }
    public function logs()
    {
        return $this->hasMany(PhaseLog::class);
    }


}
