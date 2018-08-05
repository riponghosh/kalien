<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
/**/
define('VERSION','?v='.env('APP_VER'));
/****
|loginModalBlade
****/
Route::group(['middleware' => ['locale_lan']],function(){

// Route::get('/ticketMobileTest',function(){
//     return view('mobiles.ticket');
// });
//Route::get('/telegram-updated-activity', 'TelegramBotController@updatedActivity');
//Route::get('/telegram-send-message', 'TelegramBotController@storeMessage');
Route::post('/telegram_webhook/'.env('TELEGRAM_WEB_HOOK_TOKEN'), 'TelegramBotController@getWebhookUpdates');
//Route::get('/telegram-set-webhook','TelegramBotController@setWebHook');
Route::get('/ProductMobileTest/{activity_uni_name}','TripActivityController@get_trip_activity_mobile');

/*
*  email confirm
*/
Route::get('/activation/{uni_name}/{activate_code}','ActivateAccountController@email_activate');
/*
*   Social Auth
*/
Route::get('auth/facebook/callback', 'Auth\SocialAccountController@handleCallback');
Route::get('auth/facebook/{isDirectPage?}', 'Auth\SocialAccountController@redirectToProvider');

/*
**   Facebook
*/
Route::post('facebook/messenger/auto_reply','FacebookMessengerController@auto_reply');
Route::get('facebook/messenger/merchant/auto_reply','FacebookMessengerController@merchant_auto_reply');
Route::post('facebook/messenger/merchant/auto_reply','FacebookMessengerController@merchant_auto_reply');
/*
**驗證是否登入
*/
Route::post('/authCheck', function (Request $request) {
    if (!$request::ajax()) response(array('status' => 'error','msg' => 'need api'));
    if (Auth::check()) {
        return response(array('status' => 'ok'));
    } else {
        return response(array('status' => 'error'));
    }
});
/*Use Hash Ticket*/
Route::get('/activity_ticket/use/{ticket_hash_id}','HashTicketController@use_ticket_by_hash');
/*改變語系*/
Route::get('changeLocaleLanguage/{language}','GobalController@change_web_language');
Route::get('changeCurrencyUnit/{unit}','GobalController@change_web_cur_unit');
/*隱私頁面*/
Route::get('privacyPolicy',function(){
   return view('privacyPolicy');
});
Route::get('servicePolicy', function (){
    return view('servicePolicy');
});
/*
* Landing Page
*/
Route::group(['prefix' => '/'],function(){
   if (BrowserDetect::isMobile()){
       Route::get('/', 'HomePageController@index_mobile_v2');
   }else{
       Route::get('/', 'HomePageController@index');
   }
});
//Route::get('/', 'HomePageController@index');
/*
* 活動頁面
*/
Route::get('/activity/{activity_uni_name}','TripActivityController@get_trip_activity');
/*
* 團體活動頁面
*/
Route::get('/group_events/{gp_activity_id}','UserGroupActivityController@show_group_activity');
Route::group(['middleware' => 'auth'], function () {
    /*
     * Auth Check API
     */
    Route::post('/Authorization/Check', function (Request $request) {
        if (Auth::check()) {
            return response()->json(array('success' => true));
        } else {
             return response()->json(array('success' => false, 'msg' => 'something wrong'));
        }
    });
    /*
    *  email confirm
    */
    Route::post('/activation/resend_activate_code','ActivateAccountController@resend_activate_code');
    /*
    **User
    */
    /*
     * Ticket
     */
    Route::get('/my_ticket','UserProfileController@show_user_ticket_index');
    Route::post('/activity_ticket_incidental_coupon/use','ActivityTicketController@use_activity_ticket_incidental_coupon');
    Route::post('/activity_ticket_incidental_coupon/retrieve_by_self','ActivityTicketController@retrieve_activity_ticket_incidental_coupon_by_owner');
    /*Notification*/
    Route::get('/notifications/get_all','UserNotificationController@get_all_notifications');
    Route::post('/notifications/is_read','UserNotificationController@update_notification_to_read');
    /*
    **User Profile
    */
    /*Account setting blade*/
    Route::post('/user/change_currency_unit','UserProfileController@update_currency_unit');
    Route::get('/user/abouts', 'UserProfileController@showProfile_edit');
    Route::get('/user/tripsIntroduction', 'UserProfileController@showProfile_trips_introduction');
    Route::get('/user/photos_gallery','UserProfileController@showProfile_photos_gallery');
	Route::get('/user/booking_request','UserProfileController@show_user_booking_request'); //遊客發出的
	Route::get('/user/booking_invite','UserProfileController@show_user_booking_invite'); //導遊收到的
	Route::get('/user/cart','UserProfileController@show_user_cart');
    Route::post('/send_tourist_apply_form', 'UserProfileController@apply_tourist');
    /*edit Abouts*/
    Route::post('/PUT/userProfile/summary', 'UserProfileController@edit_userProfile');
	Route::group(['middleware' => 'tourist_auth'], function () {
		Route::post('/PUT/userProfile/guideProfile', 'UserProfileController@edit_guideProfile');
		Route::post('/PUT/userProfile/userService', 'UserProfileController@edit_userProfile_service');
	});
    Route::post('/PUT/userProfile/img', 'UserProfileController@edit_userProfile_img');
    Route::post('/PUT/userProfile/photo/update','UserProfileController@edit_userProfile_photo');
    Route::post('/PUT/userProfile/photo/delete','UserProfileController@delete_userProfile_photo');
    Route::post('/PUT/userProfile/photo/description','UserProfileController@edit_userProfile_photo_description');
    Route::get('/get_tourist_apply_form', 'UserProfileController@show_tourist_apply_form');
    Route::get('/guide_application', 'UserProfileController@apply_guide');
    Route::get('/guide_status/{status}','UserProfileController@change_guide_status');
    /*
 	** Payment
    */
    Route::get('/payment','UserProfileController@show_payment');
    Route::post('/payment/refund/service_ticket','TransactionController@refund_by_us_service_ticket_id');
	/*
    ** Payment API
	*/
	Route::post('/transaction/user_ticket_direct_purchase','TransactionController@create_reciept_for_activity_ticket_direct_purchase');
	Route::post('/transaction/create_reciept_by_cart','TransactionController@create_reciept_by_cart');
	Route::post('/transaction/cart/add_items','TransactionController@add_to_cart');
    Route::post('/transaction/cart/del_items','TransactionController@del_cart_item');
    Route::group(['middleware' => 'tourist_auth'], function () {
        /*appointment*/
        Route::post('GET/appointment_request', 'UserProfileController@check_appointment_request');
        Route::get('GET/appointment_form/{guideId}', 'UserProfileController@show_appointment_request_form');
        Route::post('/send_appointment_request_form_to_guide', 'UserProfileController@send_appointment_request_to_guide');
        Route::post('/trip_appointment/discard','UserProfileController@discard_appointment_by_user_id');
        /*遊客操作*/
        Route::post('/cancel_appointment_for_guide/{appointment_id}', 'UserProfileController@cancel_appointment_for_guide');
        /***************
		 *    行程訂購api
		 **************/
		Route::post('/get_info_to_create_guide_ticket_order_in_a_date_form','GuideTicketController@show_tourist_before_order_request_form_modal');
        Route::post('/get_info_to_create_guide_ticket_order_in_a_date','GuideTicketController@get_info_to_create_guide_ticket_order_in_a_date_by_tourist');
        Route::post('/create_guide_ticket_order','GuideTicketController@create_guide_ticket_order_by_tourist');
        Route::post('/user_service_ticket_order/buyer/update','GuideTicketController@update_guide_ticket_order_by_tourist');
		Route::post('/user_service_ticket_order/buyer/delete','GuideTicketController@delete_guide_ticket_order_by_tourist');
		Route::post('/user_service_ticket_order/seller/order_response','GuideTicketController@response_guide_service_request_by_guide');   //accept reject用同一條
		Route::post('/user_service_ticket_order/seller/set_price','GuideTicketController@set_price_of_guide_service_request_by_guide');
        /*導遊操作*/
        Route::post('/accept_appointment_by_guide', 'UserProfileController@accept_appointment_by_guide');
        Route::post('/reject_appointment_by_guide/{appointment_id}', 'UserProfileController@reject_appointment_by_guide');
    });
    /*edit Trips*/
    Route::post('/trip/create', 'TripIntroductionController@create_trip');
    Route::post('/trip/del', 'TripIntroductionController@delete_trip');
    Route::post('/trip/publish', 'TripIntroductionController@published_trip');
    Route::post('/trip/update', 'TripIntroductionController@update_trip');
    Route::post('/trip/upload_main_trip_media', 'TripIntroductionController@upload_main_trip_media');
    Route::post('/trip/upload_main_trip_media_url', 'TripIntroductionController@upload_main_trip_media_url');
    Route::post('/trip/remove_main_trip_media', 'TripIntroductionController@remove_main_trip_media');
    Route::post('/trip/remove_main_trip_media_url', 'TripIntroductionController@remove_main_trip_media_url');
    /*
    **Follow User
    */
    Route::post('/FollowUser', 'UserFollowController@follow_user');
    Route::post('/DELETE/FollowUser', 'UserFollowController@unfollow_user');
    /*
    **Chat Room API
    */
    Route::get('/chatRoom/room_list','ChatRoomController@get_room_list_with_last_content'); //api
    Route::get('/chatRoom/get_info/{chatroom_id}', 'ChatRoomController@get_chat_room_by_id');  //api
    Route::post('/chatRoom/sendMsg', 'ChatRoomController@sendMsg'); //api
    Route::get('/chatRoom/getMsg/{room_id}', 'ChatRoomController@get_msg');  //api
    Route::get('/chatRoom/getMoreMsg/{room_id}/{last_msg_key_from_client}', 'ChatRoomController@get_more_msg');  //api
    /*
    **tourist Dashboard
    */
    Route::get('/GET/plans_modal', 'UserProfileController@show_plans_modal');
    /*
    **guide Dashboard
    */
    Route::group(['middleware' => ['tourist_auth']], function () {
        Route::get('/GET/tourist_request_dashboard_modal', 'UserProfileController@show_tourist_request_dashboard_modal');

        /*tourist trip appointment 資訊表單*/
        Route::get('/trip_appointment_info/response/{appointment_id}','UserProfileController@show_trip_appointment_response_info');
        Route::group(['middleware' => ['guide_auth']],function(){
            Route::get('/trip_appointment_info/request/{appointment_id}','UserProfileController@show_trip_appointment_request_info');
        });
        /*
        **團體活動
        */
        Route::post('/group_activity_api/create','UserGroupActivityController@create_gp_activity');
    });
    /*
    **Relationship API
    */
    Route::post('relationship/friendRequest/addFriend', 'RelationshipController@send_friend_request');
    Route::post('relationship/friendRequest/accept', 'RelationshipController@accept_friend_request');
    Route::post('relationship/friendRequest/reject', 'RelationshipController@reject_friend_request');
    Route::post('relationship/friendRequest/cancel', 'RelationshipController@cancel_friend_request');
    Route::post('relationship/friendRequest/unfriend', 'RelationshipController@unfriend');
    Route::get('PUT/relationship/status/{user_id}', 'RelationshipController@get_relationship_status');
    Route::get('GET/relationship/getFriendList', 'RelationshipController@get_freind_list');
    /*
    **Schedule API
    */
    Route::group(['middleware' => ['tourist_auth']], function () {
        /*guide*/
        Route::get('GET/scheduleDesk/{schedule_id}', 'ScheduleController@schedule_desk');
        Route::get('POST/schedule', 'ScheduleController@create_schedule');
        Route::get('/schedule/get_schedule/{schedule_id}','ScheduleController@get_schedule');  //api
        Route::get('/schedule/get_all_schedules','ScheduleController@get_all_schedules');  //api
        /*eventBlock*/
        //Route::post('/schedule/get_eventBlock/{eventblock_id}/{schedule_id}','ScheduleController@get_event_block');
        Route::post('/schedule/eventBlock', 'ScheduleController@event_block');
        Route::post('/schedule/eventBlock_description_image','ScheduleController@add_eventBlock_description_image');
        /*date*/
        Route::post('/schedule/add_date','ScheduleController@add_date');
        Route::post('/schedule/delete_date', 'ScheduleController@delete_date');

    });
    /*
    ** Pneko Employees
    */
    Route::group(['middleware' => ['p_employee_auth']],function(){
        Route::get('/employee','EmployeeController@show_dashboard');
        Route::get('/employee/trip_activity_gallery','EmployeeController@show_trip_activity_gallery');
        Route::get('/employee/dashboard', 'EmployeeController@show_dashboard');
        Route::get('/employee/transaction_record','EmployeeController@show_transaction_record');
        Route::get('/employee/trip_activity/create','EmployeeController@create_trip_activity');
        Route::get('/employee/trip_activity/get/{lan}/{id}','EmployeeController@show_trip_activity');
        /*活動編輯API*/
        Route::post('/employee/trip_activity/update','EmployeeController@update_trip_activity');
        Route::post('/employee/trip_activity/update/video_url','EmployeeController@update_trip_activity_video_url');
        Route::post('/employee/trip_activity/create/intro_image','EmployeeController@create_trip_activity_intro_img');
        Route::post('/employee/trip_activity/update/intro_image_info','EmployeeController@update_trip_activity_intro_img_info');
        Route::post('/employee/trip_activity/update/gallery_image','EmployeeController@update_trip_activity_gallery_image');
        Route::post('/employee/trip_activity/delete/gallery_image','EmployeeController@delete_trip_activity_gallery_image');
        /*店家*/
        Route::get('/employee/merchant_info/{merchant_uni_name}','EmployeeController@check_merchant_available');
        Route::get('/employee/merchant_info/{merchant_uni_name}/transaction_record','EmployeeController@show_merchant_transaction_record_page');
        /*票券*/
        Route::get('/employee/trip_activity_ticket/{uni_name}', 'EmployeeController@show_trip_activity_ticket');
        /*幫助客戶儲值*/
        Route::post('/employee/user_credit_api/charge','EmployeeController@charge_user_credit');
        //帳戶操作
        //Route::post('transfer_all_user_account_payable','TransactionController@transfer_all_user_account_payables'); //暫不公開
        Route::post('transfer_all_merchant_account_payable','TransactionController@transfer_all_merchant_account_payable'); //暫不公開
        /*Account Payable Contract*/
        Route::post('/employee/ac_payable_contract_record_api','EmployeeController@get_ac_payable_contract_record');//合約紀録
    });
    /*
    ** Merchant
    */
    Route::get('/merchant','MerchantController@show_merchant_dashboard'); //與dashboard 同一頁
    Route::get('/merchant/dashboard','MerchantController@show_merchant_dashboard');
    Route::get('/merchant/merchant/{merchant_uni_name}','MerchantController@show_merchant_info');
    Route::get('/merchant/activity_code_page','MerchantController@show_activity_code_page');
    Route::get('/merchant/trip_activity_ticket/{uni_name}','MerchantController@show_activity_ticket');
    Route::post('/merchant/commit_merchant_act_code','MerchantController@create_merchant_member_by_act_code');
});
Route::get('/activity_page','GuideSearchingFilterController@closure_test_page');

Auth::routes();

/*
show guide searching result
*/
Route::get('/UserSearchingFilter', 'GuideSearchingFilterController@getFilterResult');//不能用導遊，所以名稱不能統一
/*
UserProfile
*/
Route::get('/GET/userProfile/modal/{uni_name}', 'UserProfileController@index_modal');

#ChatRoom
Route::get('/chat/{id}', 'ChatRoomController@index');
});

