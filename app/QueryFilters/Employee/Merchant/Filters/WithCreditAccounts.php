<?php

namespace App\QueryFilters\Employee\Merchant\Filters;

use App\QueryFilters\IFilter;
use Illuminate\Database\Eloquent\Builder;

class WithCreditAccounts implements IFilter
{

    /**
     * Apply a given search value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $builder, $value)
    {   if($value != true) return $builder;

        return $builder->with('merchant_credit_account');
    }
}