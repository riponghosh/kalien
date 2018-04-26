<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAlbum extends Model
{
    protected $fillable = ['user_id','media_id','order'];

    public function media(){
        return $this->hasOne('App\Media','media_id','media_id');
    }
}
