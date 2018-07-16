<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use SoftDeletes;

    protected $table = 'media';
    protected $fillable = ['media_id','media_author','media_location_standard','media_location_low','media_title','media_description','media_status','media_tags','media_format','mediable_id','mediable_type'];
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'media_id';

    function mediable(){
        return $this->morphTo();
    }

}
?>

