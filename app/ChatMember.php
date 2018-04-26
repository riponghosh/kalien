<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    protected $table = 'chat_members';
    protected $fillable = ['members_id','chat_room_id','right_of_status'];

    public function chat_contents(){
        return $this->hasMany('App\ChatContent','chat_room_id','chat_room_id');
    }
    public function user(){
        return $this->hasOne('App\User','id','members_id');
    }
}

?>

