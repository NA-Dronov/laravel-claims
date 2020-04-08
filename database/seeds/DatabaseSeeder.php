<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ClaimStatusesTableSeeder::class);
        $this->call(RolesAndAbiltiesSeeder::class);
        $this->call(ManagerSeeder::class);

        if (config('app.debug')) {
            //$this->call(Dev\Seeder\UsersTableSeeder::class);
            factory(\App\Models\Claim::class, 100)->create();
        }
    }
}
