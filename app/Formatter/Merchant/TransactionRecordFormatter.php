<?php
namespace  App\Formatter\Merchant;

use App\Formatter\Interfaces\IFormatter;

class TransactionRecordFormatter implements IFormatter
{
    public function dataFormat($data, callable $closure = null)
    {
        // TODO: Implement dataFormat() method.
        if(!$data)
        {
            return [];
        }

        return $data->map(function($eachData){
            return (object) [
                'ori_amount' => $eachData->ori_amount,
                'currency_unit' => $eachData->currency_unit,
                'balanced_at' => $eachData->balanced_at,
                'settlement_time' => $eachData->settlement_time
            ];
        });

    }
}
?>