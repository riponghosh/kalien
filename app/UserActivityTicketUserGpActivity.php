<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserActivityTicketUserGpActivity extends Model
{
    protected $table = 'user_activity_ticket_user_gp_activity';

    protected $guarded = ['id'];

    public function user_group_activity(){
        return $this->hasOne('App\UserGroupActivity\UserGroupActivity','gp_activity_id','user_gp_activity_id');
    }

    protected function user_activity_ticket(){
        return $this->hasOne('App\UserActivityTicket','id','user_activity_ticket_id');
    }
}
?>