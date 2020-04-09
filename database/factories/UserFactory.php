<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    static $number = 1;

    return [
        'name' => $faker->name,
        'email' => 'user' . $number++ . '@example.com',
        'email_verified_at' => now(),
        'password' => bcrypt(1111), // password
        'remember_token' => Str::random(10),
    ];
});
