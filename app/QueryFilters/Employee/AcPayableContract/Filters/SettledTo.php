<?php

namespace App\QueryFilters\Employee\AcPayableContract\Filters;

use App\QueryFilters\IFilter;
use Illuminate\Database\Eloquent\Builder;

class SettledTo implements IFilter
{

    /**
     * Apply a given search value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $builder, $value)
    {
        if($value === null) return $builder;
        return $builder->whereDate('settlement_time', '<=', $value);
    }
}