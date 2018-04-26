<?php
namespace App\Enums;

class Pay2GoEnum
{
    const INVOICE_TYPE_B2C = 0;
    const INVOICE_TYPE_B2B = 2;
    //載具類型
    const CARRY_TYPE_PHONE_NUM = 0;
    const CARRY_TYPE_CT_DIGITAL_CERTIFICATE = 1; //自然人憑證
    const CARRY_TYPE_PAY2GO = 2;
}