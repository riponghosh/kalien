<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';

    function UserLanguage(){
    	return $this->belongsTo('App\Language','language_id','id');
    }
}
