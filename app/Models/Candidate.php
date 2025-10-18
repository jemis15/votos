<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    protected $fillable = ['name', 'identification', 'photo_url', 'event_id'];

    function cargo() : BelongsTo {
        return $this->belongsTo(Cargo::class);
    }

    function event() : BelongsTo {
        return $this->belongsTo(Event::class);
    }
}
