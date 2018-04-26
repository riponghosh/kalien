<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
	protected $table = 'user_reviews';
    protected $fillable = [
        'user_id', 'review_by_user_id','rating','content'
    ];

    public function User()
    {
        return $this->belongsTo('App\User','review_by_user_id','id');
    }

}
