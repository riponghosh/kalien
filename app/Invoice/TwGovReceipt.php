<?php
namespace App\Invoice;

use Illuminate\Database\Eloquent\Model;

class TwGovReceipt extends Model
{
    protected $table = 'invoice_tw_gov_receipts';
    protected $guarded = ['id'];
}
?>