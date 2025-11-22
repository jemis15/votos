<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = ['name'];

    function candidates(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id')
            ->wherePivotIn('role_in_room', ['candidate', 'both']);
    }

    function voters(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id')
            ->wherePivotIn('role_in_room', ['voter', 'both']);
    }

    function cargos(): HasMany
    {
        return $this->hasMany(Cargo::class);
    }

    function elections(): HasMany
    {
        return $this->hasMany(Election::class);
    }

    function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id')
            ->withPivot('role_in_room', 'cargo_id');
    }
}
