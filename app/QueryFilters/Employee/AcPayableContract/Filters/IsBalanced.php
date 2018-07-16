<?php

namespace App\QueryFilters\Employee\AcPayableContract\Filters;

use App\QueryFilters\IFilter;
use Illuminate\Database\Eloquent\Builder;

class IsBalanced implements IFilter
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
        if ($value === null) return $builder;
        if ($value === false) {
            return $builder->whereNull('balanced_at');
        }
        if ($value == true) {
            return $builder->whereNotNull('balanced_at');
        }

    }
}
