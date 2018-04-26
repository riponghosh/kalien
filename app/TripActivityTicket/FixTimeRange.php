<?php

namespace App\TripActivityTicket;

use Illuminate\Database\Eloquent\Model;

class FixTimeRange extends Model
{
    protected $table = 'trip_activity_ticket_fix_time_ranges';

    protected $guarded = ['id'];
}