<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuideServiceRequest extends Model
{

	use SoftDeletes;
	protected $table = 'guide_service_requests';
	protected $dates = ['deleted_at'];

	protected $fillable = ['*'];
	protected $guarded = ['id'];

	public function seller(){
		return $this->hasOne('App\User','id', 'request_to_id');
	}
}