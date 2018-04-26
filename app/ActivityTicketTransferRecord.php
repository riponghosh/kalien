<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityTicketTransferRecord extends Model
{
    protected $connection = 'log';
    protected $table = 'activity_ticket_transfer_records';
    protected $guarded = ['id'];

}
?>