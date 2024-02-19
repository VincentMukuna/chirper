<?php

namespace Tests\Feature\Chirp;
use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $response = $this
            ->actingAs($user)
            ->fromRoute('chirps.index')
            ->delete(route('chirps.destroy',[
                'chirp'=>$chirp->id,
            ]));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('chirps.index'));

        $this->assertNull($chirp->fresh());
    }

}
