<?php

use Faker\Generator as Faker;

$factory->define(App\Merchant\Merchant::class, function (Faker $faker) {
    return [
        'name' => 'fake Merchant',
        'pneko_charge_plan' => 2,
        'email' => 'test@test.com',
        'password' => bcrypt('abc123')
    ];
});
