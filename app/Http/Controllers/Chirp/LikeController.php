<?php

namespace App\Http\Controllers\Chirp;

use App\Events\ChirpLiked;
use App\Http\Controllers\Controller;
use App\Models\Chirp;

class LikeController extends Controller
{
    public function like(Chirp $chirp)
    {
        $chirp->likes()->attach(auth()->id());
        event(new ChirpLiked($chirp, auth()->user()));
        return back();
    }

    public function dislike(Chirp $chirp)
    {
        $chirp->likes()->detach(auth()->id());
        return back();

    }
}
