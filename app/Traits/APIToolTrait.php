<?php
namespace App\Traits;

use Illuminate\Container\Container;
use App\ViewModels\APIModel;
use App\Formatter\APIFormatter;

trait APIToolTrait
{
    public function getAPIModel($e = null)
    {
        $container = Container::getInstance();
        $apiModel = $container->make(APIModel::class);
        $apiModel->parseException($e);
        return $apiModel;
    }

    public function getAPIFormatter()
    {
        $container = Container::getInstance();
        return $container->make(APIFormatter::class);
    }
}
