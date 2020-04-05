<?php

namespace Dev\Seeder;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Менеджер',
                'email' => 'the_manager@example.com',
                'password' => bcrypt(1111),
                'email_verified_at' => now(),
                'remember_token' => \Str::random(10),
            ],
            [
                'name' => 'Завяитель Петров',
                'email' => 'petrov@example.com',
                'password' => bcrypt('123123'),
                'email_verified_at' => now(),
                'remember_token' => \Str::random(10),
            ],
        ];

        \DB::table('users')->insert($data);
    }
}
