<?php

namespace Tests\Unit\User;

use App\Models\Chirp;
use App\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_posts_method_filters_out_replies(): void
    {
        $user  = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id'=> $user->id]);
        $reply = Chirp::factory()->create(['user_id'=>$user->id]);

        $chirp->replies()->save($reply);
        $posts = $user->posts()->get();

        $this->assertFalse($posts->contains('id', $reply->id ));

    }
    public function test_replies_method_filters_out_posts(): void
    {
        $user  = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id'=> $user->id]);
        $reply = Chirp::factory()->create(['user_id'=>$user->id]);

        $chirp->replies()->save($reply);
        $replies = $user->replies()->get();

        $this->assertTrue($replies->contains('id', $reply->id ));

    }


}
