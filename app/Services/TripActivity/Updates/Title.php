<?php

namespace App\Services\TripActivity\Updates;

use App\Services\IUpdate;

class Title implements IUpdate
{

    /**
     * Apply a given search value to the builder instance.
     *
     * @param $value
     * @param mixed $value
     * @return $array
     */
    public static function apply($value)
    {
        $lan = in_array($value,['zh_tw', 'en', 'jp']) ? $value : 'zh_tw';

        return ['title_'.$lan => $value];
    }
}