<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserServiceTicket extends Model
{
    use SoftDeletes;

    protected $table = 'user_services_tickets';
    protected $fillable = ['*'];
    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

}

?>