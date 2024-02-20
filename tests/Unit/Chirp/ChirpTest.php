<?php

namespace Tests\Unit\Chirp;

use App\Models\Chirp;
use App\Models\User;
use Tests\TestCase;

class ChirpTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_reply_scope_filters_out_posts(): void
    {
        $user  = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id'=> $user->id]);
        $reply = Chirp::factory()->create(['user_id'=>$user->id]);

        $chirps=Chirp::isReply(true)->get();

        $this->assertFalse($chirps->contains('replying_to', null));
    }
}
