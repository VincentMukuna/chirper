<?php

namespace Tests\Feature\Chirp;
use App\Models\User;
use App\Notifications\NewChirp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;
use Tests\TestCase;
class ChirpCreateTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_create_chirp()
    {
        $user = User::factory()->create();
        $follower = User::factory()->create();

        $user->followers()->attach($follower);

        Notification::fake();
        $response = $this->postChirp($user);

        Notification::assertSentTo($follower, NewChirp::class);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chirps.index'));
        $this->assertEquals('test', $user->refresh()->chirps()->first()->message);
    }

    public function test_cannot_create_chirp_with_blank_message()
    {
        $user = User::factory()->create();
        $response = $this->postChirp($user, '');

        $response
            ->assertSessionHasErrors('message')
            ->assertRedirect(route('chirps.index'));

        $this->assertFalse($user->refresh()->chirps()->exists());
    }



    public function test_notification_has_required_values()
    {
        $user = User::factory()->create();
        $follower = User::factory()->create();

        $user->followers()->attach($follower);

        Notification::fake();
        $this->postChirp($user);
        Notification::assertSentTo(
            $follower,
            NewChirp::class,
            function ($notification){
                $this->assertObjectHasProperty('chirp', $notification);
                $this->assertEquals('test', $notification->chirp->message);
                return true;
            }
        );

    }
    private function postChirp($user, $message = 'test')
    {
        return $this
            ->actingAs($user)
            ->from(route('chirps.index'))
            ->post(route('chirps.store'), [
                'message'=>$message,
            ]);
    }
}
