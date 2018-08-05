<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\API\Web\Transaction\PayController;

class PayControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp()
    {
        parent::setUp();
        $user = factory(App\User::class)->create(['email' => 'test2@test.com']);
        $user->credit_account()->save(factory(App\UserCreditAccount::class)->make());
        $merchant = factory(App\Merchant\Merchant::class)->create(['email' => 'test2@test.com']);
        $product = factory(App\Models\Product::class)->create(['merchant_id' => $merchant->id]);
        $tripActivityTicket = factory(App\Models\TripActivityTicket::class)->create([
            'trip_activity_id' => $product->id,
            'merchant_id' => $merchant->id
        ]);
        $userGroupActivity = factory(App\UserGroupActivity\UserGroupActivity::class)->create(['activity_ticket_id' => $tripActivityTicket->id]);
        $receipt = factory(App\Receipt::class)->create([
            'user_id' => $user->id,
            'product_id' => $tripActivityTicket->id,
            'product_type' => $product->pdt_type,
            'start_date' => $userGroupActivity->start_date,
            'transfer_incidental_coupon_to_user_id' => $user->id,
            'relate_gp_activity_id' => $userGroupActivity->gp_activity_id,
            'start_time' => $userGroupActivity->start_time,
        ]);
    }
    
    public function testPay_Success_UserCredit()
    {
        $user = App\User::latest()->first();
        // var_dump($user->toArray());
        $request_data = [
            //'prime_token' => 'required',
            /*發票資料*/
            'invoice_type' => 0,  //2=三聯，0＝二聯
            'B2B_id' => null,
            'receipt_carry_type' => 0,
            'receipt_carry_num' => '0000000',
            'receipt_donation_code' => null,
            'address_for_lottery_mailing' => 'fake address',
            /*user資料*/
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'phone_area_code' => $user->phone_area_code,
            /*user credit*/
            'user_credit_using_amt' => 1000 //使用者帳戶內的餘額
        ];
        $response = $this->actingAs($user)->post('api-web/v1/transaction/pay', $request_data);
        // var_dump($response);
        $response->assertJson(["success" => "true"]);
    }

    public function testPay_Success_CreditCard()
    {
        $user = App\User::latest()->first();

        $request_data = [
            'prime_token' => 'test_3a2fb2b7e892b914a03c95dd4dd5dc7970c908df67a49527c0a648b2bc9',
            /*發票資料*/
            'invoice_type' => 0,  //2=三聯，0＝二聯
            'B2B_id' => null,
            'receipt_carry_type' => 0,
            'receipt_carry_num' => '0000000',
            'receipt_donation_code' => null,
            'address_for_lottery_mailing' => 'fake address',
            /*user資料*/
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'phone_area_code' => $user->phone_area_code,
        ];
        $response = $this->actingAs($user)->post('api-web/v1/transaction/pay', $request_data);
        $response->assertJson(["success" => "true"]);
    }
}
