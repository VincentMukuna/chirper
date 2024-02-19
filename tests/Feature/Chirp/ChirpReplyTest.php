<?php

namespace Tests\Feature\Chirp;

use App\Models\Chirp;
use App\Models\User;
use App\Notifications\ReplyChirp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;
use Tests\TestCase;

class ChirpReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_can_be_replied_to():void
    {
        $chirper = User::factory()->create();
        $replier = User::factory()->create();

        $originalChirp = Chirp::factory()->create([
                'user_id'=>$chirper->id
            ]);
        Notification::fake();
        $response = $this
            ->actingAs($replier)
            ->from(route('chirps.show', ['chirp'=>$originalChirp->id]))
            ->post(route('chirps.store'),[
                'replying_to'=>$originalChirp->id,
                'message'=>'reply',
            ]);

        Notification::assertSentTo($chirper, ReplyChirp::class);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chirps.show', ['chirp'=>$originalChirp->id]));

        $this->assertTrue($originalChirp->refresh()
                ->replies()
                ->first()
                ->message === 'reply'
        );
    }

    public function test_cannot_reply_to_a_nonexistent_post():void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('chirps.index'))
            ->post(route('chirps.store'),[
                'replying_to'=>'1',
                'message'=>'reply',
            ]);

        $response
            ->assertSessionHasErrors('replying_to')
            ->assertRedirect(route('chirps.index'));
    }
}
