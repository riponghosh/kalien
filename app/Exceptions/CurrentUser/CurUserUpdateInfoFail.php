<?php

namespace App\Exceptions\CurrentUser;

use App\Exceptions\JsonResponseException;

class CurUserUpdateInfoFail extends JsonResponseException
{
    protected $error = 'UpdateCurrentUserInfoFailed';
    protected $message = '資料更新失敗';
    protected $code = '1001';

}