<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

interface IUpdate
{
    /**
     * Apply a given search value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return $array
     */
    public static function apply($value);
}