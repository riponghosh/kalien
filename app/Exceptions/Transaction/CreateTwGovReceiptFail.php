<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\JsonResponseException;

class CreateTwGovReceiptFail extends JsonResponseException
{
    protected $error = 'Create Tw Gov Receipt Fail';
    protected $message = '開立失敗';
    protected $code = '1001';
}