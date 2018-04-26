<?php
namespace App\Traits;

use Illuminate\Container\Container;
trait ContainerTrait
{
    public function getContainer()
    {
        return Container::getInstance();
    }
}