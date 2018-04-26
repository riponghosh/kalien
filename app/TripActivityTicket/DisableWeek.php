<?php

namespace App\TripActivityTicket;

use Illuminate\Database\Eloquent\Model;

class DisableWeek extends Model
{
    protected $table = 'trip_activity_ticket_disable_weeks';

    protected $guarded = ['id'];
}