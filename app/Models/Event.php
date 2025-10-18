<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = ['name'];

    function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }
}
