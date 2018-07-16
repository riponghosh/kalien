<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TripActivityRefundRule extends Model
{
    use SoftDeletes;

    protected $table = 'trip_activity_refund_rules';
}