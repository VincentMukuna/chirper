<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $appends=[
        'is_like',
        'is_rechirp',
        'likes_count',
        'rechirps_count',
        'replies_count',
        'chirper',
        'rechirped_chirp',
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




    public function repliesCount():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rechirping'])){
                    $chirp = Chirp::find($attributes['rechirping']);
                    return $chirp->replies()->count();
                }
                return $this->replies()->count();
            }
        );
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

    public function isLike(): Attribute
    {

        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rechirping'])){
                    $chirp = Chirp::find($attributes['rechirping']);
                    return $chirp->likes()->where('user_id', auth()->id())->exists();
                }
                return $this->likes()->where('user_id', auth()->id())->exists();
            },
            set: function ($value) {
                if($value){
                    $this->likes()->attach(auth()->id());
                }else{
                    $this->likes()->detach(auth()->id());
                }
            }
        );

    }

    public function likesCount():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rechirping'])){
                    $chirp = Chirp::find($attributes['rechirping']);
                    return $chirp->likes()->count();
                }
                return $this->likes()->count();
            }
        );
    }

    public function isRechirp():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rechirping'])){
                    $chirp = Chirp::find($attributes['rechirping']);
                    return $chirp->rechirps()->where('user_id', auth()->id())->exists();
                }
                return $this->rechirps()->where('user_id', auth()->id())->exists();
            }
        );

    }

    public function rechirpsCount():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rechirping'])){
                    $chirp = Chirp::find($attributes['rechirping']);
                    return $chirp->rechirps()->count();
                }
                return $this->rechirps()->count();
            }
        );
    }

    public function chirper():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rechirping'])){
                    $chirp = Chirp::find($attributes['rechirping']);
                    return $chirp->user;
                }
                return $this->user;
            }
        );
    }

    public function rechirpedChirp():Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if(isset($attributes['rechirping'])){
                    return Chirp::find($attributes['rechirping']);
                }
                return null;
            }
        );
    }

}
