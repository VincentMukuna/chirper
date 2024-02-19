<?php

namespace App\Http\Controllers;

use App\Events\ChirpCreated;
use App\Events\ChirpLiked;
use App\Events\ChirpRepliedTo;
use App\Models\Chirp;
use App\Models\User;
use App\Notifications\LikeChirp;
use App\Notifications\ReplyChirp;
use App\Rules\ChirpExists;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\Inertia;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $chirps = Chirp
            ::isReply(false)
            ->with('user:id,name')
            ->withCount('likes')
            ->latest()
            ->get();

        $userId = auth()->id();
        $chirps->map(function ($chirp) use ($userId) {
            $chirp->isLike = $chirp->likes()->where('user_id', $userId)->exists();
            return $chirp;
        });

        return Inertia::render('Chirps/Index',
            [
                'chirps'=>$chirps,
                'warning'=>['This is a warning']
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'replying_to'=>['nullable', new ChirpExists()]
        ]);

        $chirp = new Chirp($validated);
        $request->user()->chirps()->save($chirp);
        if($request->filled('replying_to')){
            $originalChirp = Chirp::find($validated['replying_to']);
            ChirpRepliedTo::dispatch($originalChirp,$chirp,$request->user());
        }else{
            ChirpCreated::dispatch($chirp);
        }
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
       $chirp = Chirp::with(['user:id,name', 'replies', 'replies.user:id,name'])
           ->withCount('likes')
           ->findOrFail($chirp->id);

       $chirp->isLike = $chirp->likes()->where('user_id', auth()->id())->exists();
        return Inertia::render('Chirps/Show',
        [
            'chirp'=>$chirp
        ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp):RedirectResponse
    {
        $this->authorize('update', $chirp);

        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $chirp->update($validated);

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp):RedirectResponse
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();

        return redirect(route('chirps.index'));
    }

    public function like(Chirp $chirp)
    {
        $chirp->likes()->attach(auth()->id());
        ChirpLiked::dispatch($chirp, auth()->user());
        return back();
    }

    public function dislike(Chirp $chirp)
    {
        $chirp->likes()->detach(auth()->id());
        return back();

    }
}
