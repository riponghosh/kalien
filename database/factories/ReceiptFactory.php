<?php

use Faker\Generator as Faker;

$factory->define(App\Receipt::class, function (Faker $faker) {
    return [
        'qty' => 1,
    ];
});
