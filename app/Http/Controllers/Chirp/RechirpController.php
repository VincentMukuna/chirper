<?php

namespace App\Http\Controllers\Chirp;

use App\Events\ChirpRechirped;
use App\Http\Controllers\Controller;
use App\Models\Chirp;
use Illuminate\Http\Request;

class RechirpController extends Controller
{
    public function rechirp( Request $request, Chirp $chirp)
    {
        $rechirper = auth()->user();

        if(auth()->user()->chirps()->where('rechirping', $chirp->id)->exists()){
            return back()->withErrors([
                'rechirping'=>'You have already rechirped this chirp'
            ]);
        }
        $rechirp = new Chirp([
            'message'=>$chirp->message,
        ]);

        $rechirp->forceFill([
            'user_id' => $rechirper->id,
            'rechirping'=>$chirp->id,
        ]);

        $rechirp->save();


        event(new ChirpRechirped($chirp,$rechirp));
        return back();
    }

    public function undo_rechirp(Request $request, Chirp $chirp)
    {
        $chirp->rechirps()->where('user_id', auth()->id())->delete();
    }
}
