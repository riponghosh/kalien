<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $fillable = ['*'];
    protected $guarded = ['id'];

    public function tw_gov_receipt(){
        return $this->hasOne('App\Invoice\TwGovReceipt', 'invoice_id', 'id');
    }

    public function invoice_items(){
        return $this->hasMany('App\InvoiceItem','invoice_id', 'id');
    }
}

?>