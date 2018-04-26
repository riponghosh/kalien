<?php
namespace  App\Formatter\Merchant;

use App\Formatter\Interfaces\IFormatter;

class CurMerchantFormatter implements IFormatter
{
    public function dataFormat($data, callable $closure = null)
    {
        // TODO: Implement dataFormat() method.
        if(!$data)
        {
            return [];
        }

        return (object) [
            'tel' => $data->tel,
            'tel_area_code' => $data->tel_area_code,
            'name' => $data->name,
            'address' => $data->address,
            'bank_account' => optional($data->merchant_credit_account)->bank_account,
            'acc_credit' => optional($data->merchant_credit_account)->credit,
            'acc_credit_unit' => optional($data->merchant_credit_account)->currency_unit
        ];
    }
}