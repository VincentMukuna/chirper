<?php

namespace App\Http\Controllers;

use App\Events\ChirpLiked;
use App\Models\Chirp;
use App\Notifications\LikeChirp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $chirps = Chirp::with('user:id,name')->latest()->withCount('likes')->get();

        $userId = auth()->id();
        $chirps->map(function ($chirp) use ($userId) {
            $chirp->isLike = $chirp->likes()->where('user_id', $userId)->exists();
            return $chirp;
        });

        return Inertia::render('Chirps/Index', compact('chirps'));
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

        $request->user()->chirps()->create($validated);
        return redirect(route('chirps.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        //
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
    }

    public function dislike(Chirp $chirp)
    {
        $chirp->likes()->detach(auth()->id());
    }
}
