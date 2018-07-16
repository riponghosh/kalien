<?php

namespace App\Services\BankTransferDataBuildService;

abstract class BankTransferDataBuildService
{
    function __construct()
    {
    }

    abstract function bank_data_converter($data, &$err_report);

    public function space_fill_out($value, $side, $total_length){
        if(($space_len = $total_length - strlen($value)) < 0) return false;
        $val_string = str_pad($value, $total_length, chr(32), ($side == 'l' ? STR_PAD_LEFT : STR_PAD_RIGHT));

        return $val_string;
    }

    public function zero_fill_out($value, $side, $total_length){
        if(($space_len = $total_length - strlen($value)) < 0) return false;
        $val_string = str_pad($value, $total_length, '0', ($side == 'l' ? STR_PAD_LEFT : STR_PAD_RIGHT));

        return $val_string;
    }
}