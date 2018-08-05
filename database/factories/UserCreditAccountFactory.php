<?php

use Faker\Generator as Faker;

$factory->define(App\UserCreditAccount::class, function (Faker $faker) {
    return [
        'credit' => 1000.00,
        'currency_unit' => 'TWD',
        'bank_account' => '4242424242424242'
    ];
});
