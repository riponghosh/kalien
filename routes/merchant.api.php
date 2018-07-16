<?php

Route::group([
    'prefix' => 'v1',
    'namespace' => 'API\Web\Merchant',
    'middleware' => ['locale_lan']
], function ($app){
    $app->post('/register','AuthController@register');
    $app->post('/login','AuthController@authenticated');
    Route::group(['middleware' => 'jwt.auth'],function ($app){
        $app->post('/logout','AuthController@logout');
    });
    Route::group([
        'middleware' => 'jwt.auth'
    ],function ($app){
        $app->post('/merchant','MerchantController@get_current');

        Route::group([
            'prefix' => '/product',
            'namespace' => 'Product'
        ], function ($app){
            $app->post('/', 'ProductController@get');
            Route::group([
                'namespace' => 'GroupTypePdt'
            ], function ($app){
                $app->post('/disable_dates/create', 'GroupTypePdtController@add_disable_date');
                $app->post('/disable_dates/delete', 'GroupTypePdtController@delete_disable_date');

            });
        });
        Route::group([
            'prefix' => 'group_activity'
            ],function ($app){
                $app->post('/get','GroupActivityController@get_by_trip_activity_ticket_id');
                $app->post('/cancel', 'GroupActivityController@cancel_gp_activity');
        });

        Route::group([
            'prefix' => 'transaction'
            ],function ($app){
                $app->post('/records', 'TransactionController@get_sales_record');
        });
    });
});

?>