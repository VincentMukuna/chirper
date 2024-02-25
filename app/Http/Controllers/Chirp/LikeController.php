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
        $chirp->save();
        event(new ChirpLiked($chirp, auth()->user()));
        return back();
    }

    public function dislike(Chirp $chirp)
    {
        $chirp->likes()->detach(auth()->id());
        $chirp->save();
        return back();

    }

    public function toggle(Chirp $chirp)
    {
        $chirp->likes()->toggle(auth()->id());
        return back();
    }
}
