<?php

namespace Dev\Seeder;

use Illuminate\Database\Seeder;

class DebugDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Fake Users and assign client role
        factory(\App\Models\User::class, 15)->create()->each(function ($u) {
            /**
             * @var \App\Models\User $u
             */
            $u->assignRole('client');
        });

        factory(\App\Models\Claim::class, 300)->create();
    }
}
