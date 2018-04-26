<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;

    protected $table = 'invoice_items';
    protected $fillable = ['*'];
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    public function invoice(){
        return $this->belongsTo('App\Invoice', 'invoice_id', 'id');
    }
}

?>