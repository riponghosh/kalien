<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\API\Web\Transaction\PayController;

class PayControllerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }
    
    public function testPaySuccess()
    {
        $request_data = [
            'invoice_type' => 'required|in:0,2',
            'B2B_id' => 'required_if:invoice_type,2',
            'receipt_carry_type' => null,
            'email' => 'test@test.com',
            'phone_number' => '090000000000',
            'phone_area_code' => 886,
            'user_credit_using_amt' => 1
        ];
        $response = $this->post('api-web/v1/transaction/pay', $request_data);
        $response->assertJson(['success' => true]);
    }
}
