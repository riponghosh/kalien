<?php
namespace App\UserGroupActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGpActivityApplicants extends Model
{
    use SoftDeletes;

    protected $table = 'user_gp_activity_applicants';
    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    public function user(){
        return $this->hasOne('App\User', 'id', 'applicant_id');
    }

    public function Group_activity(){
        return $this->belongsTo('App\UserGroupActivity\UserGroupActivity','user_gp_activity_id', 'id');
    }
}
?>