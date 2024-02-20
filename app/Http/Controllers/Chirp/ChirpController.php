<?php

namespace App\Http\Controllers\Chirp;

use App\Events\ChirpCreated;
use App\Events\ChirpRepliedTo;
use App\Http\Controllers\Controller;
use App\Models\Chirp;
use App\Rules\ChirpExists;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user= auth()->user();
        $chirps = Chirp
            ::with(['user:id,name', 'originalChirp','originalChirp.user:id,name'])
            ->withCount(['likes', 'rechirps', 'replies'])
            ->latest()
            ->isReply(false)
            ->limit(10)
            ->get()
            ->map(function (Chirp $chirp) use ($user) {
                $chirp->isLike = $user->likedChirps()->where('chirp_id', $chirp->id)->exists();
                $chirp->isRechirp = $chirp->rechirps()->where('user_id', $user->id)->exists();
                return $chirp;
            })
        ;


        return Inertia::render('Chirps/Index',
            [
                'chirps'=>$chirps,
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
        ]);

        $chirp = new Chirp($validated);
        $request->user()->chirps()->save($chirp);
        ChirpCreated::dispatch($chirp);
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
       $chirp = Chirp::with([
           'user:id,name',
           'replies',
           'replies.user:id,name',
           'inReplyTo',
           'inReplyTo.user:id,name'])
           ->withCount('likes', 'replies')
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
}
