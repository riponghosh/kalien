<?php

namespace App\UserActivityTicket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class TicketRefunding extends Model
{
    use SoftDeletes;

    protected $table = 'user_activity_ticket_refundings';

    protected $dates = ['deleted_at'];

    protected $guarded = ['id'];

}
?>