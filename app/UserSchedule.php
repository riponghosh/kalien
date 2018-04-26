<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class UserSchedule extends Model
{
    protected $table = 'user_schedules';

    protected $fillable = [
        'owner_id','schedule_id'
    ];
}
?>

