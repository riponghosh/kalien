<?php

namespace App\QueryFilters\Employee\GroupActivity\Filters;

use App\QueryFilters\IFilter;
use Illuminate\Database\Eloquent\Builder;

class StartDateBefore implements IFilter
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
        if(empty($value))return $builder;
        return $builder->whereDate('start_date', '<=' , $value);
    }
}
