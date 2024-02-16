<?php

namespace App\Models;

use App\Events\ChirpCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chirp extends Model
{
    use HasFactory;


    protected $fillable = [
        'message'
    ];

    protected $hidden=[
    ];

    protected $dispatchesEvents = [
        'created' => ChirpCreated::class,
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chirp_likes', 'chirp_id', 'user_id');
    }

}
