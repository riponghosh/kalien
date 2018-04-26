<?php

namespace App\Formatter;

use App\Formatter\Interfaces\IAPIFormatter;
use App\ViewModels\IViewModel;
use Illuminate\Contracts\Routing\ResponseFactory;

class APIFormatter implements IAPIFormatter
{

    private $response;
    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    public function success(IViewModel $apiModel, $token = null, callable $closure = null)
    {
        $apiModelArr = $apiModel->toArray();
        $apiModelArr['success'] = true;
        $json = $this->response->json($apiModelArr,200);
        return $token != null ? $json->header('Authorization', $token) : $json;
    }
    public function error(IViewModel $apiModel, callable $closure = null)
    {
        $apiModelArr = $apiModel->toArray();
        $apiModelArr['success'] = false;
        return $this->response->json($apiModelArr,200);
    }

}