<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Claim;
use App\Models\ClaimStatus;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Claim::class, function (Faker $faker) {

    $manager = User::whereHas('roles', function ($query) {
        $query->where('roles.name', 'manager');
    })->first();
    $manager_id = rand(0, 100) > 70 ? $manager->user_id : 0;

    $subject = $faker->sentence(rand(3, 8), true);
    $body = $faker->realText(rand(1000, 4000));
    $statuses = [ClaimStatus::OPEN, ClaimStatus::CLOSED];
    $status = $manager_id == 1 ? ClaimStatus::PROCESSED : $statuses[array_rand($statuses)];

    $created_at = $faker->dateTimeBetween('-3 month', '-2 days');

    return [
        'user_id' => User::where('email', '!=', env('ADMIN_EMAIL'))->orderByRaw('RAND()')->first()->user_id,
        'manager_id' => $manager_id,
        'subject' => $subject,
        'body' => $body,
        'status' => $status,
        'created_at' => $created_at,
        'updated_at' => $created_at,
    ];
});
