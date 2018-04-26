<?php
namespace App\Formatter\Interfaces;
use App\ViewModels\IViewModel;

interface IAPIFormatter {
    public function success(IViewModel $apiModel, $token, callable $closure = null);
    public function error(IViewModel $apiModel, callable $closure = null);
}