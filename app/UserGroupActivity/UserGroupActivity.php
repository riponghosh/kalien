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
        return $this->hasOne('App\Models\TripActivityTicket','id', 'activity_ticket_id');
    }

    public function applicants(){
        return $this->hasMany('App\UserGroupActivity\UserGpActivityApplicants', 'user_gp_activity_id', 'id');
    }

    public function host(){
        return $this->hasOne('App\User','id','host_id');
    }

    public function trip_activity(){
        return $this->trip_activity_ticket->hasOne('App\Models\Product', 'id', 'trip_activity_id');
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
    /*
    *  Scope
    */
    public function scopeDurationBetween($query, $startDateAfter = null, $startDateBefore = null){
        if(!(empty($startDateAfter) && empty($startDateBefore))){
            !empty($startDateAfter) ? $query->whereDate('start_date','>=',$startDateAfter) : null;
            !empty($startDateBefore) ? $query->whereDate('start_date','<=',$startDateBefore) : null;
        }

        return $query;

    }

    public function scopeNotBegin($query){
        return $query->whereDate('start_at','<=', date('y-m-d H:i:s'));
    }

    public function scopeHasTitle($query){
        return $query->whereNotNull('activity_title')->where('activity_title','!=', '');
    }

    public function scopeExpired($query){
        return $query->whereDate('start_at','<', date('y-m-d H:i:s'));
    }


}
?>