<?php

namespace Tests\Feature\Chirp;
use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
class ChirpActionsTest extends TestCase
{
    use RefreshDatabase;
    public function test_chirp_can_be_deleted():void
    {
        $user = User::factory()->create();
        $chirp=Chirp::factory()->create([
            'user_id'=>$user->id,
        ]);

        $response = $this->deleteChirp($user, $chirp);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chirps.index'));

        $this->assertDatabaseMissing(Chirp::class, [
            'id'=>$chirp->id,
        ]);
    }

    private function deleteChirp(User $user, Chirp $chirp): TestResponse
    {
        return $this
            ->actingAs($user)
            ->fromRoute('chirps.index')
            ->delete(route('chirps.destroy',[
                'chirp'=>$chirp->id,
            ]));
    }

    public function test_other_users_cant_delete_chirp():void
    {
        $chirper = User::factory()->create();
        $chirp = Chirp::factory()->create([
            'user_id'=>$chirper->id,
        ]);

        $otherUser = User::factory()->create();

        $response = $this->deleteChirp($otherUser, $chirp);

        $response
            ->assertStatus(403);

        $this->assertTrue($chirper->refresh()->chirps()->count()===1);

    }

}
