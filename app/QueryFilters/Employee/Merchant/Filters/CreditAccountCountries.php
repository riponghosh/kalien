<?php

namespace App\QueryFilters\Employee\Merchant\Filters;

use App\QueryFilters\IFilter;
use Illuminate\Database\Eloquent\Builder;

class CreditAccountCountries implements IFilter
{

    /**
     * Apply a given search value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $builder, $value)
    {   if(empty($value) || gettype($value) !== 'array') return $builder;

        return $builder->whereHas('merchant_credit_account', function ($q) use ($value){
            $q->whereIn('bank_country', $value);
        });
    }
}