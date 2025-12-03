<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'identification',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factory_recovery_codes',
        'remember_token',
        'profile_photo_path'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['image_url'];

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->profile_photo_path) {
            return null; // Imagen por defecto si no tiene
        }

        // Si la imagen se guardó con store('images', 'public')
        return \Illuminate\Support\Facades\Storage::url($this->profile_photo_path);
    }

    function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'room_user', 'user_id', 'room_id')
            ->wherePivotIn('role_in_room', ['voter', 'both']);
    }

    function votes()
    {
        return $this->hasMany(Election::class, 'votes');
    }

    function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }
}
