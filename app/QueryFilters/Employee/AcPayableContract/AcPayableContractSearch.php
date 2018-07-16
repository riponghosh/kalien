<?php
namespace App\QueryFilters\Employee\AcPayableContract;

use App\AccountPayableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AcPayableContractSearch
{
    public static function apply(Request $filters)
    {
        $query = (new AccountPayableContract())->newQuery();

        $query = static::applyFiltersToQuery($filters, $query);

        return $query->limit(40)->get();
    }

    private static function applyFiltersToQuery(
        Request $filters, Builder $query) {
        foreach ($filters->all() as $filterName => $value) {

            $decorator =
                __NAMESPACE__ . '\\Filters\\' .
                str_replace(' ', '', ucwords(
                    str_replace('_', ' ', $filterName)));

            if (class_exists($decorator)) {
                $query = $decorator::apply($query, $value);
            }

        }

        return $query;
    }
}
?>