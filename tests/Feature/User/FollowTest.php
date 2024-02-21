<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_another_user()
    {
        $user = User::factory()->create();
        $follower  = User::factory()->create();

        Notification::fake();
        $response = $this->followUser($user, $follower);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('user.show', ['user'=>$user->id]);

        Notification::assertSentTo($user, NewFollower::class);
        $this->assertEquals(1, $user->refresh()->followers()->count());
        $this->assertEquals($follower->refresh()->id,$user->refresh()->followers()->first()->id);
    }

    private function followUser(User $user, User $follower): TestResponse
    {
        return $this
            ->actingAs($follower)
            ->fromRoute('user.show', ['user'=>$user->id])
            ->post(route('user.follow', ['user'=>$user->id]));
    }

    public function test_user_cannot_follow_themselves()
    {
        $user = User::factory()->create();
        Notification::fake();
        $response = $this->followUser($user, $user);

        $response
            ->assertSessionHasErrors('follow')
            ->assertRedirectToRoute('user.show', ['user'=>$user->id]);

        Notification::assertNothingSentTo($user);

        $this->assertEquals(0, $user->refresh()->followers()->count());
        $this->assertEquals(0, $user->refresh()->following()->count());
    }
}
