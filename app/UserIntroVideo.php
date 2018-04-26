<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class UserIntroVideo extends Model
{
    protected $table = 'user_intro_videos';

    protected $fillable = [
        'id','media_id','user_id','media_type'
    ];

    public function media(){
        return $this->hasOne('App\Media','media_id','media_id');
    }
}
?>

