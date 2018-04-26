<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $table = 'chat_rooms';
    protected $fillable = ['id','type'];

    public function chat_members(){
        return $this->hasMany('App\ChatMember','chat_room_id','id');
    }
    public function chat_contents(){
        return $this->hasMany('App\ChatContent','chat_room_id','id');
    }
}

?>

