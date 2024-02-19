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
         User::factory(10)
             ->hasChirps(5)
             ->create();
          User::factory(1)
             ->hasChirps(5)
             ->create([
                 'password'=>Hash::make('password'),
                 'email'=>'test@gmail.com'
             ]);


//         Chirp::all()->map(function (Chirp $chirp){
//             $chirp->replies()->createMany(Chirp::factory(5)
//                 ->for(User::inRandomOrder()->first())
//                 ->create());
//         });
    }
}
