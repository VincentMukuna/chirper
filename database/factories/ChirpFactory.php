<?php

namespace Database\Factories;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chirp>
 */
class ChirpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=>User::inRandomOrder()->first()->id,
            'message'=>fake()->realText(100),
        ];
    }

    public function replies():Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id'=>User::inRandomOrder()->first()->id,
                'replying_to'=>Chirp::inRandomOrder()->first()->id
            ];
        });
    }
}
