<?php
namespace App\ViewModels;

use App\ViewModels\IViewModel;
use Illuminate\Validation\ValidationException;

class APIModel implements IViewModel
{
    protected $code = 0;
    protected $msg = '';
    protected $data = [];

    public function __construct($e = null)
    {
        if (!is_null($e)) {
            $this->parseException($e);
        }
    }

    public function parseException($e)
    {
        if ($e) {
            $this->code = $e->getCode();
            if ($e instanceof ValidationException) {

                $this->msg = collect($e->errors())->flatten()->reduce(function ($carry, $item) {
                    return $carry . $item;
                });

            } else {
                $this->msg = $e->getMessage();
            }
            $this->data = null;
        }
    }
    public function setCode($code)
    {
        $this->code = $code;
    }
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }
    public function setData($data)
    {
        $this->data = $data;
    }
    public function getCode()
    {
        return $this->code;
    }
    public function getMsg()
    {
        return $this->msg;
    }
    public function getData()
    {
        return $this->data;
    }
    public function clearModel()
    {
        $this->code = 0;
        $this->msg = '';
        $this->data = [];
    }
    public function toArray()
    {
        return [
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data,
        ];
    }
}
