<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Claim;
use Faker\Generator as Faker;

$factory->define(Claim::class, function (Faker $faker) {

    $user_id = 2;
    $manager_id = rand(0, 100) > 70 ? 1 : 0;

    $subject = $faker->sentence(rand(3, 8), true);
    $body = $faker->realText(rand(1000, 4000));
    $statuses = ['O', 'C'];
    $status = $manager_id == 1 ? 'P' : $statuses[array_rand($statuses)];

    $created_at = $faker->dateTimeBetween('-3 month', '-2 days');

    return [
        'user_id' => $user_id,
        'manager_id' => $manager_id,
        'subject' => $subject,
        'body' => $body,
        'status' => $status,
        'created_at' => $created_at,
        'updated_at' => $created_at,
    ];
});
