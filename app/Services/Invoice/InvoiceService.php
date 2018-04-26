<?php

namespace App\Services\Invoice;

use App\Repositories\Invoice\InvoiceRepo;
use League\Flysystem\Exception;

class InvoiceService
{
    protected $repo;

    function __construct(InvoiceRepo $invoiceRepo)
    {
        $this->repo = $invoiceRepo;
    }
//-----------------------------------------------------
//  Ite,
//-----------------------------------------------------
    function get_item_by_invoice_item_id($invoice_item_id){
        return $this->repo->first_item_by_id($invoice_item_id);
    }

    function del_item($invoice_item_id, $qty = null){
        $invoice_item = $this->repo->first_by_item_id($invoice_item_id);
        if($qty == null || $invoice_item->qty - $qty == 0){
            $action = $this->repo->del_item($invoice_item_id);
        }elseif($invoice_item->qty > $qty){
            $action = $this->repo->item_update($invoice_item_id, ['qty' => ($invoice_item->qty - $qty)]);
        }else{
            throw new Exception();
        }
        if(!$action) throw new Exception('失敗。');
        return true;
    }
}