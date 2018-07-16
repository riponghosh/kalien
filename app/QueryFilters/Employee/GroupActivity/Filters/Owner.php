<?php

namespace App\QueryFilters\Employee\GroupActivity\Filters;

use App\QueryFilters\IFilter;
use Illuminate\Database\Eloquent\Builder;

class Owner implements IFilter
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
        return $builder->where('host_id', $value);
    }
}