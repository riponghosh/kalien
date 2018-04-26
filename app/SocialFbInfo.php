<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialFbInfo extends Model
{
    protected $table = 'social_fb_info';

    protected $fillable = ['user_id', 'fb_id', 'fb_email', 'avatar_url'];
}
?>

