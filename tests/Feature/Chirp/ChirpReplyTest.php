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

        $chirp = Chirp::factory()->create([
                'user_id'=>$chirper->id
            ]);
        Notification::fake();
        $response = $this->postReply($replier, route('chirps.show', ['chirp'=>$chirp->id]), $chirp->id);

        Notification::assertSentTo($chirper, ReplyChirp::class);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chirps.show', ['chirp'=>$chirp->id]));

        $this->assertDatabaseHas('chirps', [
            'replying_to'=>$chirp->id,
        ]);

        $this->assertDatabaseCount('chirps',2);
        $this->assertTrue($chirp->refresh()->replies()->count()===1);

    }

    public function test_cannot_reply_to_a_nonexistent_post():void
    {
        $user = User::factory()->create();

        $response = $this->postReply($user, route('chirps.index'));

        $response
            ->assertNotFound();

        $this->assertDatabaseMissing('chirps', [

        ]);
    }

    public function test_replies_are_not_deleted_after_chirp_deleted():void
    {
        $user = User::factory()->create();
        $chirp = Chirp::factory()->create([
            'user_id'=>$user->id
        ]);

        Notification::fake();
        $response = $this
            ->actingAs($user)
            ->from(route('chirps.show', ['chirp'=>$chirp->id]))
            ->post(route('chirps.reply',['chirp'=>$chirp->id]),[
                'message'=>'reply',
            ]);
        Notification::assertNothingSentTo($user);

        $response = $this
            ->actingAs($user)
            ->from(route('chirps.show', ['chirp'=>$chirp->id]))
            ->delete(route('chirps.destroy',['chirp'=>$chirp->id]));

        $this->assertDatabaseCount('chirps', 1);
    }

    public function test_notification_has_correct_data()
    {
        $chirper = User::factory()->create();
        $replier = User::factory()->create();

        $chirp = Chirp::factory()->create([
            'user_id'=>$chirper->id
        ]);
        Notification::fake();
        $this->postReply($replier,route('chirps.show', [$chirp->id]), $chirp->id);

        Notification::assertSentTo(
            $chirper,
            ReplyChirp::class,
            function ($notification)use ($replier, $chirp){
                $this->assertObjectHasProperty('reply', $notification);
                $this->assertEquals($replier->id, $notification->reply->user->id);
                return true;
            }
        );

    }

    public function test_user_doesnt_receive_notification_of_own_reply()
    {
        $user = User::factory()->create();
        $chirp = Chirp::factory()->create([
            'user_id'=>$user->id
        ]);

        Notification::fake();
        $this->postReply($user, route('chirps.show', [$chirp->id]), $chirp->id);

        Notification::assertNothingSent();


    }

    private function postReply(User $user,string $from,$id='non_existent')
    {
        return $this
            ->actingAs($user)
            ->from($from)
            ->post(route('chirps.reply', ['chirp'=>$id]),[
                'message'=>'reply',
            ]);

    }
}
