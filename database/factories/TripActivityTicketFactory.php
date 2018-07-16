<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TripActivityTicket::class, function (Faker $faker) {
    return [
        'name_zh_tw' => $faker->name,
        'amount' => $faker->randomNumber(3),
        'currency_unit' => 'TWD',
        'qty_unit' => 1,
        'qty_unit_type' => 'hour',
        'available' => 1,
        'trip_activity_id' => 1
    ];
});
