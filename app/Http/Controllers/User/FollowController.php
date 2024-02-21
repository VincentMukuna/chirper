<?php

namespace App\Http\Controllers\User;

use App\Events\UserFollowed;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
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
        event(new UserFollowed($user, auth()->user()));
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
