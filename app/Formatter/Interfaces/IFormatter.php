<?php
namespace App\Formatter\Interfaces;
interface IFormatter {
    public function dataFormat($data, callable $closure = null);
}