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
             'name' => 'Ekenekiso Ugbanawaji',
             'email' => 'l.ekenekiso@ugbanawaji.com',
             'password' => 'password'
         ]);

         $user->assignRoles('user');
         UserProfile::factory()
             ->create([
                 'user_id' => $user->id
             ]);
    }
}
