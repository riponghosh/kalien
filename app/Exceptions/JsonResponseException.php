<?php

namespace App\Exceptions;

use Exception;

abstract class JsonResponseException extends Exception
{
    protected $data = [];
    protected $resMsg = '';  //前台信息

    public function getExceptionError()
    {
        return $this->error;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getResMsg()
    {
        return $this->resMsg;
    }

}
