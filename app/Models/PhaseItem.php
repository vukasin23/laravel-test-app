<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhaseItem extends Model
{
    protected $fillable = [
        'phase_id',
        'number',
        'date',
        'is_done',
    ];
    public $timestamps = false;

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }
}
