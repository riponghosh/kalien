<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TripActivityMedia extends Model
{
    protected $table = 'trip_activity_media';
    protected $fillable = ['trip_activity_id', 'media_id','is_gallery_image','media_type', 'description_zh_tw'];

    public function media(){
        return $this->hasOne('App\Media','media_id','media_id');
    }
}
?>