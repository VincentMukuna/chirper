<?php

namespace Tests\Feature\Chirp;
use App\Events\ChirpLiked;
use App\Models\Chirp;
use App\Models\User;
use App\Notifications\LikeChirp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Mockery\Matcher\Not;
use Tests\TestCase;
class ChirpLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_like_chirp():void
    {
        $user = User::factory()->create();
        $liker = User::factory()->create();

        $chirp = Chirp::factory()->create([
            'user_id'=>$user->id,
        ]);

        Notification::fake();

        $response = $this->likeChirp($liker, $chirp);

        Notification::assertSentTo(
            $user,
            LikeChirp::class,
            function($notification, $channels) use($chirp, $liker){
                return $notification->liker->id === $liker->id && $notification->chirp->id===$chirp->id;
        });

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chirps.index'));
        $this->assertTrue($chirp->refresh()->likes()->count()===1);
    }

    private function likeChirp(User $liker, Chirp $chirp): \Illuminate\Testing\TestResponse
    {
        return $this
            ->actingAs($liker)
            ->fromRoute('chirps.index')
            ->patch(route('chirps.like', [
                'chirp'=>$chirp->id,
            ]));

    }

    public function test_cannot_like_a_nonexistent_post():void
    {
        $user = User::factory()->create();
        Event::fake();

        $response = $this
            ->actingAs($user)
            ->from(route('chirps.index'))
            ->patch(route('chirps.like', [
                'chirp'=>"non_existent_id",
            ]));


        $response
            ->assertNotFound();

        $this->assertTrue($user->refresh()->likedChirps()->count()===0);
        Event::assertNotDispatched(ChirpLiked::class);

    }

    public function test_chirper_receives_chirp_liked_notification():void
    {
        $user = User::factory()->create();
        $liker = User::factory()->create();

        $chirp = Chirp::factory()->create([
            'user_id'=>$user->id,
        ]);

       Notification::fake();
        $response = $this->likeChirp($liker, $chirp);
       Notification::assertSentTo($user, LikeChirp::class);
    }
}
