<?php
Route::group(['middleware' => ['locale_lan']],function() {
    Route::group([
        'prefix' => 'v1'
    ], function ($app) {
        Route::group([
            'namespace' => 'API\Web'
        ], function ($app) {
            Route::group([
                'prefix' => 'user_activity_ticket',
                'namespace' => 'UserActivityTicket',
                'middleware' => ['web_api_auth']
            ], function ($app){
                $app->post('use','UserActivityTicketController@use_ticket');
                $app->post('/authorized_to','UserActivityTicketController@authorized_to');
                $app->post('refund', 'TicketRefundController@activity_ticket_refund');
            });
            Route::group([
                'prefix' => 'activity_ticket',
                'namespace' => 'ActivityTicket'
            ], function ($app) {
                $app->post('get_ticket_available_purchase_dates_and_time_ranges', 'ActivityTicketController@get_activity_ticket_all_sold_date_and_time_ranges');
            });
            Route::group([
                'prefix' => 'transaction',
                'namespace' => 'Transaction',
                'middleware' => ['web_api_auth']
            ], function ($app) {
                $app->post('pay', 'PayController@pay');
                $app->post('test', 'PayController@test');
            });
            Route::group([
                'prefix' => 'group_activity',
                'namespace' => 'GroupActivity',
                'middleware' => ['web_api_auth']
            ], function ($app){
                $app->post('apply_for_join_in', 'GroupActivityController@apply_for_join_in');
                $app->post('/{gp_activity_id}/participant/change_by_ticket_authorized','GroupActivityController@change_participant_by_ticket_authorized');
            });
            Route::group([
                'prefix' => 'employee',
                'namespace' => 'Employee',
                'middleware' => ['p_employee_auth']
            ], function ($app){
                $app->post('/group_activity/get','MerchantController@get_gp_activities_by_trip_activity_ticket_id');
                Route::group([
                    'prefix' => 'group_activity',
                ],function ($app){
                    $app->post('/pdt_has_stock', 'GroupActivityController@update_gp_activity_has_pdt_stock');
                });
                Route::group([
                    'prefix' => 'merchant'
                ],function ($app){
                    $app->post('/account_withdrawal', 'MerchantController@merchant_account_withdrawal');
                });
            });
        });
    });
});
?>