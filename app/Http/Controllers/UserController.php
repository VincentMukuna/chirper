<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;

class UserController extends Controller
{
    public function profile(User $user){
        return Inertia::render('Users/UserProfile', [
            'user'=> User::withCount('followers')
                ->withCount('following')
                ->with('followers')
                ->findOrFail($user->id),
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
        $validator = Validator::make(['user_id'=>$user->id],[
            'user_id'=>'different:'.auth()->id()
            ],
            [
                'user_id.different'=>'You cannot follow yourself. '
            ]
        );

        if($validator->fails()){
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if($user->isFollowing(auth()->id())){
            return back()->with('warning', "Already following $user->name");
        }

        auth()->user()->following()->attach($user->id);
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
