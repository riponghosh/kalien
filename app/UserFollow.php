<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    protected $table = 'user_follows';

   	protected $fillable = [
        'user_id', 'followed_user_id'
    ];

    public function User()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
?>
