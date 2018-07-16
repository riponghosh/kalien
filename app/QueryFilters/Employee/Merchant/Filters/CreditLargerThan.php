<?php

namespace App\QueryFilters\Employee\Merchant\Filters;

use App\QueryFilters\IFilter;
use Illuminate\Database\Eloquent\Builder;

class CreditLargerThan implements IFilter
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
        if($value == null) return $builder;
        return $builder->whereHas('merchant_credit_account', function ($q) use($value) {
            $q->where('credit','>=', $value)->where('currency_unit', CLIENT_CUR_UNIT);
        });
    }
}