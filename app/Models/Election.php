<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    protected $fillable = [
        'event_id',
        'cargo_id',
        'status',
    ];

    function candidates(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'eligibles', 'election_id', 'candidate_id');
    }

    function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    function hasMinimumActiveCandidates(int $min = 2): bool
    {
        // Si la relación ya está cargada, utilice la coleccion.
        if ($this->relationLoaded('candidates')) {
            return $this->candidates->count() >= $min;
        }

        // De lo contrario, consulta la base de datos.
        return $this->candidates()->count() >= $min;
    }
}
