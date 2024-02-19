<?php

namespace Tests\Feature\Chirp;
use App\Models\User;
use App\Notifications\NewChirp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ChirpCreateTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_create_chirp()
    {
        $user = User::factory()->create();
        $follower = User::factory()->create();

        $user->followers()->attach($follower);

        \Notification::fake();

        $response = $this
            ->actingAs($user)
            ->from(route('chirps.index'))
            ->post(route('chirps.store'), [
                'message'=>'test',
            ]);
        \Notification::assertSentTo($follower, NewChirp::class);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chirps.index'));
        $this->assertEquals('test', $user->refresh()->chirps()->first()->message);
    }

    public function test_cannot_create_chirp_with_blank_message()
    {
        $user = User::factory()->create();
        $response = $this
            ->actingAs($user)
            ->from(route('chirps.index'))
            ->post(route('chirps.store'), [
                'message'=>'',
            ]);

        $response
            ->assertSessionHasErrors('message')
            ->assertRedirect(route('chirps.index'));

        $this->assertFalse($user->refresh()->chirps()->exists());
    }



}
