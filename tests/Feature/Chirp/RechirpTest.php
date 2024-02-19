<?php

namespace Tests\Feature\Chirp;

use App\Models\Chirp;
use App\Models\User;
use App\Notifications\RechirpChirp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Matcher\Not;
use Notification;
use Tests\TestCase;

class RechirpTest extends TestCase
{
    use RefreshDatabase;
    public function test_users_can_rechirp_chirps():void
    {
        $chirper = User::factory()->create();
        $chirp=Chirp::factory()->create([
            'user_id'=>$chirper->id,
        ]);

        $rechirper = User::factory()->create();

        Notification::fake();

        $response = $this->rechirp($rechirper, $chirp);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('chirps.index');

        Notification::assertSentTo($chirper, RechirpChirp::class);
        Notification::assertCount(1);


        $this->assertDatabaseHas('chirps', [
            'rechirping'=>$chirp->id,
        ]);

    }

    public function test_user_can_rechirp_chirp_only_once():void
    {
        $chirper = User::factory()->create();
        $chirp=Chirp::factory()->create([
            'user_id'=>$chirper->id,
        ]);

        $rechirper = User::factory()->create();

        $this
            ->rechirp($rechirper, $chirp)
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('chirps.index');
        $response = $this->rechirp($rechirper, $chirp);

        $response
            ->assertSessionHasErrors('rechirping')
            ->assertRedirectToRoute('chirps.index');


        $this->assertTrue(Chirp::where('rechirping', $chirp->id)->count()===1);
        $this->assertTrue($chirp->refresh()->rechirps()->count()===1);

    }

    public function test_user_doesnt_receive_notification_if_rechirp_own_chirp():void
    {
        $user = User::factory()->create();
        $chirp=Chirp::factory()->create([
            'user_id'=>$user->id,
        ]);

        Notification::fake();
        $response = $this->rechirp($user, $chirp);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('chirps.index');
        Notification::assertNothingSentTo($user);

        $this->assertDatabaseHas('chirps', [
            'rechirping'=>$chirp->id,
        ]);

    }

    private  function rechirp(User $rechirper, Chirp $chirp)
    {
        return $this
            ->actingAs($rechirper)
            ->fromRoute('chirps.index')
            ->post(route('chirps.rechirp', ['chirp'=>$chirp->id]));

    }



}
