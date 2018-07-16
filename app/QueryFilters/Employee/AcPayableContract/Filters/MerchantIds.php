<?php

namespace App\QueryFilters\Employee\AcPayableContract\Filters;

use App\QueryFilters\IFilter;
use Illuminate\Database\Eloquent\Builder;

class MerchantIds implements IFilter
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
        if(empty($value)) return $builder;
        return $builder->whereIn('merchant_id', $value);

    }
}