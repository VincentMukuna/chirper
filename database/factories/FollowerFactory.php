<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FollowerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=> User::inRandomOrder()->first()->id,
            'follower_id'=> function ($attributes) {
                //make sure user is not following themselves or already following the user
                return User::query()
                    ->where('id','!=',$attributes['user_id'])
                    ->whereNotIn('id',function ($query) use ($attributes) {
                        $query->select('follower_id')
                            ->from('followers')
                            ->where('user_id',$attributes['user_id'])
                            ->orWhere('follower_id',$attributes['user_id'])
                        ;
                    })
                    ->inRandomOrder()
                    ->first()->id;
            },
        ];
    }
}
