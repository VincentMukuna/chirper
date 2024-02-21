<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(\Illuminate\Http\Request $request){
        $validated = $request->validate([
            'search'=>'nullable|string'
        ]);



        $currentUser=auth()->user();
        $users = User::query()
            ->when($request->filled('search'), function ($query) use($validated){
                $query->where('name', 'LIKE', "%{$validated['search']}%");
                return $query;
            })
            ->limit(10)
            ->whereNot("id", auth()->id())
            ->get()
            ->map(function (User $user) use ($currentUser) {
                $user->isFollow = $currentUser->following()->where('user_id', $user->id)->exists();
                return $user;
            })
        ;
        return Inertia::render('Users/Index', [
            'users'=>$users,
            'search'=>$request->get('search', '')
        ]);
    }
    public function show(User $user){
        return Inertia::render('Users/Show', [
            'user'=> User
                ::withCount([
                    'following',
                    'followers'
                ])
                ->with(
                    [
                        'posts',
                        'likedChirps.chirp',
                        'likedChirps.chirp.user',
                        'posts.user:id,name',
                        'replies',
                        'replies.user:id,name',
                        'posts.originalChirp',
                        'posts.originalChirp.user:id,name'
                    ])
                ->findOrFail($user->id)
            ,
            'userFollows'=> $user->isFollowedBy(auth()->id())

        ]);
    }



}
