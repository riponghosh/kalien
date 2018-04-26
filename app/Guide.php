<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $table = 'guides';
    protected $fillable = ['user_id','status','charge_per_day','currency_unit'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    /**
     * 取得擁有此電話的使用者。
     */
    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
    public function userServices(){
    	return $this->hasMany('App\UserService','user_id', 'user_id');
	}
    public function guideServicePlace(){
        return $this->hasMany('App\GuideServicePlace','guide_id','user_id');
    }
}
