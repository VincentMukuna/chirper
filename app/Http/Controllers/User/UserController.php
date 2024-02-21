<?php

namespace App\Http\Controllers\User;

use App\Events\UserFollowed;
use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
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
