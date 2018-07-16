<?php

Route::group([
    'prefix' => 'v1',
    'namespace' => 'API\Web\Employee',
    'middleware' => ['locale_lan']
], function ($app){
    $app->post('/login','AuthController@authenticated');

    Route::group([
        'middleware' => 'jwt.auth'
    ],function ($app){
        Route::group([
           'prefix' => '/product',
            'namespace' => 'Product'
        ],function ($app){
            $app->post('/package/{package_id}/gp_buying_status/{gp_buying_status_id}/img/upload','ProductController@upload_gp_buying_status_img');
            $app->post('{id}','ProductController@show');
            $app->post('{id}/update','ProductController@update');
        });
        Route::group([
            'prefix' => '/group_activity',
            'namespace' => 'GroupActivity'
        ],function ($app){
            $app->post('/','GroupActivityController@get');
            $app->post('/pdt_has_stock', 'GroupActivityController@update_gp_activity_has_pdt_stock');
        });
        Route::post('/merchants', 'MerchantController@get');
        Route::group([
            'prefix' => 'merchants',
            'namespace' => 'Merchant',
        ],function ($app){
            Route::group([
                'prefix' => '/credit_accounts'
            ],function ($app){
                Route::post('/bank_transfer_data', 'CreditAccountController@get_bank_transfer_data');
                Route::post('withdraws_multi_account', 'CreditAccountController@withdraws_multi_account');
            });
        });
        Route::group([
            'prefix' => '/merchant',
            //'namespace' => 'Merchant',
        ],function ($app){
            $app->post('/{id}', 'MerchantController@show');
            $app->post('/{id}/products', 'MerchantController@show_products');
           // $app->post('/{id}/payable_contracts', 'MerchantController@show_payable_contract');
        });
        $app->post('/transaction_contracts','TransactionContractController@show_payable_contract');
        Route::group([
            'prefix' => '/payable_contract'
        ],function ($app){
            $app->post('/settlement', 'TransactionContractController@settlement');
        });
    });
});
