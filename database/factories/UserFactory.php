<?php

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name, 
        'password' => bcrypt('abc123'),
        'email' => 'test@test.com',
        'sex' => 'M',
        'country' => '020',
        'phone_number' => '0900000000',
        'phone_area_code' => 886,
        'birth_date' => '1993-12-29'
    ];
});
