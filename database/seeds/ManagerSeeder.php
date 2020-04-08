<?php

use App\Models\Ability;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manager = User::create([
            'name' => env('ADMIN_NAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => bcrypt(env('ADMIN_PASSWORD')),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $manager_role = Role::where('name', 'manager')->first();

        $manager->assignRole($manager_role);
    }
}
