<?php

namespace App\Exceptions\Transaction\TapPay;

use App\Exceptions\JsonResponseException;

class TapPayPayFail extends JsonResponseException
{
    protected $error = 'TapPayPayFail';
    protected $message = '付款失敗';
    protected $code = '1001';
}