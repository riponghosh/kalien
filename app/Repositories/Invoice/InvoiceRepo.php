<?php

namespace App\Repositories\Invoice;

use App\Invoice;
use App\InvoiceItem;

class InvoiceRepo
{
    protected $model;
    protected $invoiceItem;

    function __construct(Invoice $invoice, InvoiceItem $invoiceItem)
    {
        $this->model = $invoice;
        $this->invoiceItem = $invoiceItem;
    }

    function first_by_item_id($item_id){
        return $this->model->whereHas('invoice_items', function ($q) use ($item_id){
            $q->where('id', $item_id);
        })->first();
    }

    function first_item_by_id($item_id){
        return $this->invoiceItem->find($item_id);
    }

    function del_item($item_id){
        return $this->invoiceItem->find($item_id)->delete();
    }

    function item_update($item_id, $data){
        return $this->invoiceItem->find($item_id)->update($data);
    }

}