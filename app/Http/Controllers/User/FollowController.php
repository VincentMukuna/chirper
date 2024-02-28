<?php

namespace App\Http\Controllers\User;

use App\Events\UserFollowed;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function following(User $user){
        return $user->following()->get();
    }
    public function followers(User $user){
        return $user->followers()->get();
    }
    public function toggleFollow(User $user){
        if ($user->is(auth()->user())){
            return back()->withErrors([
                'follow'=>'Cannot follow yourself',
            ]);
        };
        $user->followers()->toggle(auth()->id());
        $user->refresh();

        if ($user->followers()->where('follower_id', auth()->id())->exists()){

            event(new UserFollowed($user, auth()->user()));
        }

        return back();
    }
    public function create(User $user){

        if ($user->is(auth()->user())){
            return back()->withErrors([
                'follow'=>'Cannot follow yourself',
            ]);
        }

        auth()->user()->following()->attach($user->id);
        event(new UserFollowed($user, auth()->user()));
        return back();
    }

    public function destroy(User $user){
        if(!$user->isFollowedBy(auth()->id())){
            return back()->with('warning', "Not following $user->name");
        }

        auth()->user()->following()->detach($user->id);

        return back();
    }
}
