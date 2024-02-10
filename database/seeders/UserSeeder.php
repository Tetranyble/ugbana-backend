<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $user = User::factory()->create([
             'firstname' => 'Ugbanawaji',
             'lastname' => 'Ekenekiso',
             'middlename' => 'Leonard',
             'email' => 'senenerst@gmail.com',
             'password' => 'password'
         ]);

         $user->assignRoles('user');
         UserProfile::factory()
             ->create([
                 'user_id' => $user->id
             ]);
        $user = User::factory()->create([
            'firstname' => 'Movies',
            'lastname' => 'Web',
            'email' => 'movieswebbs@gmail.com',
            'password' => 'password'
        ]);

        $user->assignRoles('user');
        UserProfile::factory()
            ->create([
                'user_id' => $user->id
            ]);
    }
}
