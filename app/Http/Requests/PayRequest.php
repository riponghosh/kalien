<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayRequest extends FormRequest
{
    public function rules(){
        return [
          'prime_token' => 'required',
          /*發票資料*/
          'invoice_type' => 'required|in:0,2',
          'B2B_id' => 'required_if:invoice_type,2',
          'receipt_carry_type' => 'in:0,1,2,null',
          'receipt_carry_num' => 'required_if:receipt_carry_type,0,1,2|min:7',
          'receipt_donation_code' => 'nullable|min:4',
          /*user資料*/
          'email' => 'required|email',
          'phone_number' => 'required',
          'phone_area_code' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'prime_token.required' => '交易發生問題'
        ];
    }

    public function authorize(){
        return true;
    }
}