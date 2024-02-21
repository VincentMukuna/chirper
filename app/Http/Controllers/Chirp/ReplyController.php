<?php

namespace App\Http\Controllers\Chirp;

use App\Events\ChirpRepliedTo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChirpRequest;
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

    public function store(ChirpRequest $request, Chirp $chirp)
    {
        $reply = new Chirp($request->only('message'));
        $reply['user_id'] = auth()->id();

       $reply->inReplyTo()->associate($chirp);

       $reply->save();

        event(new ChirpRepliedTo($reply));

        return back();
    }
}
