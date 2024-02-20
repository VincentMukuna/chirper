<?php

namespace App\Http\Controllers\Chirp;

use App\Events\ChirpRepliedTo;
use App\Http\Controllers\Controller;
use App\Models\Chirp;
use App\Rules\ChirpExists;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    public function index()
    {

    }

    public function show()
    {

    }

    public function store(Chirp $chirp)
    {
        $validated = request()->validate([
            'message' => 'required|string|max:255',
        ]);
        $reply = new Chirp($validated);
        $reply['user_id'] = auth()->id();

       $reply->inReplyTo()->associate($chirp);

       $reply->save();

        ChirpRepliedTo::dispatch($chirp, $reply, auth()->user());


        return back();
    }
}
