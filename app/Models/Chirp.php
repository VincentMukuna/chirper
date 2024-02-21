<?php

namespace App\Models;

use App\Events\ChirpCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chirp extends Model
{
    use HasFactory;


    protected $fillable = [
        'message',
        'replying_to'
    ];

    protected $hidden=[
    ];



    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes():BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'chirp_likes', 'chirp_id', 'user_id')
            ->withTimestamps();
    }



    public function replies():HasMany
    {
        return $this->hasMany(Chirp::class, 'replying_to');

    }



    public function rechirps():HasMany
    {
        return $this->hasMany(Chirp::class, 'rechirping');

    }

    public function originalChirp():BelongsTo
    {
        return $this->belongsTo(Chirp::class, 'rechirping');
    }



    public function scopeIsReply(Builder $query, $reply=true): Builder
    {
        if($reply){
            return $query->whereNotNull('replying_to');
        }else{
            return $query->whereNull('replying_to');
        }

    }

    public function inReplyTo():BelongsTo
    {
        return $this->belongsTo(Chirp::class, 'replying_to');
    }



}
