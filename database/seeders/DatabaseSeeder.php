<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Chirp;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)
        ->hasChirps(1)
        ->create([
            'name'=>'Test User',
            'password'=>Hash::make('password'),
            'email'=>'test@gmail.com'
        ]);
        User::factory(1)
        ->hasChirps(1)
        ->create([
            'name'=>'Test User2',
            'password'=>Hash::make('password'),
            'email'=>'test2@gmail.com'
        ]);
         User::factory(5)
             ->hasChirps(2)
             ->create();

    }
}
