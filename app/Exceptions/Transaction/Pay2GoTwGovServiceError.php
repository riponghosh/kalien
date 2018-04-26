<?php

namespace App\Exceptions\Transaction;

use App\Exceptions\JsonResponseException;

class Pay2GoTwGovServiceError extends JsonResponseException
{
    protected $error = 'Create Tw Gov Receipt Fail';
    protected $message = '失敗';
    protected $code = '1001';
}