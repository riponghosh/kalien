<?php
namespace  App\Formatter\TripActivity;

use App\Formatter\Interfaces\IFormatter;

class PackageDisableDatesFormatter implements IFormatter
{
    public function dataFormat($data, callable $closure = null)
    {
        // TODO: Implement dataFormat() method.
        if (!$data) {
            return [];
        }

        /*
         * Input: 所有日期rows
         * Output: array of dates
         *
         */
        return array_pluck($data, 'date');
    }
}