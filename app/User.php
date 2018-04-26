<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Services\ResetPasswordService;

class User extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password','email','birth_date','country','sex','phone_number','phone_area_code','activate_code','uni_name','social_fb_id', 'last_login_at', 'socket_token'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activate_code', 'social_fb_id', 'socket_token', 'last_login_at'
    ];

    public function sendPasswordResetNotification($token)
    {

        $resetPW = new ResetPasswordService();
        $resetPW->send_mail($this->getEmailForPasswordReset(), $token);
        //$emailAPI = new EmailAPI(new AccessAPI());
        //$emailAPI->reset_password('derrickho723@gmail.com','www.pneko.com/password/reset/'.$token);
    }

    /**
    * ORM
    */
    public function trip(){
        return $this->hasMany('App\Trip','trip_author','id');
    }
    public function guide()
    {
        return $this->hasOne('App\Guide','user_id','id');
    }
    public function user_icon(){
        return $this->user_icons()->with('media')->where('is_used', 1);
    }
    public function user_icons()
    {
        return $this->hasMany('App\UserIcon','user_id','id');
    }
    public function user_albums(){
        return $this->hasMany('App\UserAlbum','user_id','id');
    }
    public function review()
    {
       return $this->hasMany('App\UserReview','user_id','id'); 
    }
    public function intro_video()
    {
        return $this->hasMany('App\UserIntroVideo','user_id','id');
    }
    public function languages()
    {
        return $this->hasMany('App\UserLanguage','user_id','id');
    }
    public function user_follows(){
        return $this->hasMany('App\UserFollow','user_id','id');
    }
    /*被follow*/
    public function user_is_followed(){
       return $this->hasMany('App\UserFollow','id','id'); 
    }
    public function user_services(){
    	return $this->hasMany('App\UserService', 'user_id', 'id');
	}

}
