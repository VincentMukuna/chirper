<?php

namespace App\Http\Controllers;

use App\Events\UserFollowed;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;

class UserController extends Controller
{
    public function profile(User $user){
        return Inertia::render('Users/UserProfile', [
            'user'=> User::withCount('followers')
                ->withCount(['following'])
                ->with(
                    [
                        'posts',
                        'likedChirps.chirp',
                        'likedChirps.chirp.user',
                        'posts.user:id,name',
                        'replies',
                        'replies.user:id,name'
                    ])
                ->findOrFail($user->id)
            ,
            'userFollows'=> $user->isFollowedBy(auth()->id())

        ]);
    }

    public function toggleFollow(User $user){

        if ($user->isFollowedBy(auth()->id())){
            return $this->unfollow($user);
        }else{
            return $this->follow($user);
        }
    }
    public function follow( User $user){

        if ($user->is(auth()->user())){
           return back()->withErrors([
               'follow'=>'Cannot follow yourself',
           ]);
        }

        auth()->user()->following()->attach($user->id);
        UserFollowed::dispatch($user, auth()->user());
        return back();
    }

    public function unfollow(User $user){
        if(!$user->isFollowedBy(auth()->id())){
            return back()->with('warning', "Not following $user->name");
        }

        auth()->user()->following()->detach($user->id);

        return back();
    }

}
