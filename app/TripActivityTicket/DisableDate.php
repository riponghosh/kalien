<?php

namespace App\TripActivityTicket;

use Illuminate\Database\Eloquent\Model;

class DisableDate extends Model
{
    protected $table = 'trip_activity_ticket_disable_dates';

    protected $guarded = ['id'];
}