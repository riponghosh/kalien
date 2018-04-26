<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AppointmentDate extends Model
{

    protected $table = 'appointment_dates';
    protected $fillable = ['guide_tourist_matches_id', 'id', 'date'];

}
?>

