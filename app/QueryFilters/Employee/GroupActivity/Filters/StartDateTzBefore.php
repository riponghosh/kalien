<?php

namespace App\QueryFilters\Employee\GroupActivity\Filters;

use App\QueryFilters\IFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class StartDateTzBefore implements IFilter
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
        $date_tz = Carbon::createFromFormat('Y-m-d', $value)->timezone(TZ)->toDateTimeString();
        return $builder->whereDate('start_date', '<=' , $date_tz);
    }
}
