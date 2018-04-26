<?php
namespace App\UserGroupActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroupActivity extends Model
{
    use SoftDeletes;

    protected $table = 'user_gp_activities';
    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    public function user_activity_tickets(){
        return $this->belongsToMany(
            'App\UserActivityTicket',
            'user_activity_ticket_user_gp_activity',
            'user_gp_activity_id',
            'user_activity_ticket_id',
            'gp_activity_id',
            'id'
        );
    }
    public function trip_activity_ticket(){
        return $this->hasOne('App\TripActivityTicket','id', 'activity_ticket_id');
    }

    public function applicants(){
        return $this->hasMany('App\UserGroupActivity\UserGpActivityApplicants', 'user_gp_activity_id', 'id');
    }

    public function host(){
        return $this->hasOne('App\User','id','host_id');
    }

    public function trip_activity(){
        return $this->trip_activity_ticket->hasOne('App\Product', 'id', 'trip_activity_id');
    }

    public function block_for_conflict(){
        return $this->hasOne('App\UserGroupActivity\UserGpActivityBlockForConflict', 'id', 'blocked_by');
    }
    //被blocked的group
    public function blocked_by_for_conflict(){
        return $this->hasOne('App\UserGroupActivity\UserGpActivityBlockForConflict', 'id', 'blocked_gp_activity_id');
    }

    public function relate_user_activity_tickets(){
        return $this->hasMany('App\UserActivityTicketUserGpActivity', 'user_gp_activity_id', 'gp_activity_id');
    }
    public static function boot()
    {
        parent::boot();
        self::deleting(function($model)
        {
            $model->block_for_conflict()->delete();
        });
    }

}
?>