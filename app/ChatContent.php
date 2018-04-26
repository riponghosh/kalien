<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatContent extends Model
{
    protected $table = 'chat_contents';
    protected $fillable = ['chat_room_id','sent_by_member_id','content'];

    public function chat_room(){
        return $this->belongsTo('App\ChatRoom','chat_room_id','id');
    }
}

?>

