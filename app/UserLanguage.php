<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLanguage extends Model
{
    protected $table = 'user_languages';

    public function User()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
    public function language(){
    	return $this->hasOne('App\Language','id','language_id');
    }
}
