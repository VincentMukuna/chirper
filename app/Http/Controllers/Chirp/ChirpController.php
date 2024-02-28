<?php

namespace App\Http\Controllers\Chirp;

use App\Events\ChirpCreated;
use App\Events\ChirpRepliedTo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChirpRequest;
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
            ::with(['user:id,name'])
            ->latest()
            ->isReply(false)
            ->paginate()
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
    public function store(ChirpRequest $request)
    {
        $chirp = new Chirp($request->only(['message']));
        $request->user()->chirps()->save($chirp);
        event(new ChirpCreated($chirp));
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
           ->findOrFail($chirp->id);
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
    public function update(ChirpRequest $request, Chirp $chirp):RedirectResponse
    {
        $this->authorize('update', $chirp);

        $chirp->update($request->validated());

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp):RedirectResponse
    {
        $this->authorize('delete', $chirp);
        $chirp->rechirps()->delete();
        $chirp->delete();
        return redirect(route('chirps.index'));
    }
}
