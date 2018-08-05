<?php

use Faker\Generator as Faker;

$factory->define(App\UserGroupActivity\UserGroupActivity::class, function (Faker $faker) {
    $id = App\UserGroupActivity\UserGroupActivity::latest()->first()->id + 1;
    return [
        'host_id' => 1,
        'activity_title' => null,
        'start_date' => '2018-08-31',
        'start_time' => '12:00:00',
        'start_at' => '2018-08-31 05:00:00',
        'timezone' => 'Asia/Taipei',
        'duration' => 60,
        'duration_unit' => 'min',
        'need_min_joiner_for_avl_gp' => 0,
        'has_pdt_stock' => 1,
        'limit_joiner' => null,
        'gp_activity_id' => '10001'.date('ymd').$id
    ];
});
