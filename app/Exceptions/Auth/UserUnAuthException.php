<?php

namespace App\Exceptions\Auth;

use App\Exceptions\JsonResponseException;

class UserUnAuthException extends JsonResponseException
{
    protected $error = 'Please Login';
    protected $message = '請先登入';
    protected $code = '2001';
}