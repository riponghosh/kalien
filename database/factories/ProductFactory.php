<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Product::class, function (Faker $faker) {
    $name = 'test';
    return [
        'title_zh_tw' => $name,
        'pdt_type' => 2,
        'time_zone' => 'Asia/Taipei',
        'uni_name' => '1-'. $name,
        'open_time' => '13:00',
        'close_time' => '20:00'
    ];
});
