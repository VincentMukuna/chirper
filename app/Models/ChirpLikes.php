<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChirpLikes extends Model
{
    use HasFactory;

    function chirp(): BelongsTo
    {
        return $this->belongsTo(Chirp::class);
    }

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
