<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserIcon extends Model
{
    protected $table = 'user_icons';
    protected $fillable = ['user_id','media_id','is_used'];

    public function media()
    {
        return $this->hasOne('App\Media','media_id','media_id');
    }
}
?>

