<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function chirps(): HasMany
    {
        return $this->hasMany(Chirp::class)->latest();
    }

    public function posts():HasMany
    {
        return $this
            ->hasMany(Chirp::class)
            ->whereNull('replying_to')
            ->latest();
    }

    public function replies()
    {
        return $this->chirps()->whereNotNull('replying_to');

    }

    public function likedChirps():hasMany
    {
        return $this->hasMany(ChirpLikes::class )->latest();
    }

    public function followers():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')
            ->withTimestamps();
    }

    public function following():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function isFollowing(string $id):bool
    {
        return $this->following()->where('user_id', $id)->exists();
    }

    public function isFollowedBy(string $id):bool
    {
        return $this->followers()->where('follower_id', $id)->exists();
    }


}
