<?php
namespace App\Http\Controllers;
use App\User;
use App\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class UserFollowController extends Controller{

	/*reponse to clicent*/
	public $success;
	public $error;
	public function __construct()
	{
		$this->success = array('status' => 'ok');
		$this->error = array('status' => 'error');
	}
	/*
	**追蹤與取消Ajax
	*/
	public function follow_user(Request $request){
		if($request->ajax()){
			if(Auth::user()->id == $request->user_id) return false;
			return DB::transaction(function ()use($request) {
				$user = User::where('id',$request->user_id)->firstOrFail();
				if(!$user) return response($this->error);
				$result = UserFollow::firstOrCreate(['user_id' => Auth::user()->id,
													'followed_user_id'=>$request->user_id
				]);
				if($result){ 
					return $this->success;
				}else{
					return $this->error;
				}

			});
		}
	}
	public function unfollow_user(Request $request){
		if($request->ajax()){
			$result = UserFollow::where('user_id',Auth::user()->id)->where('followed_user_id',$request->user_id)->delete();
		};
		if($result){ 
			return response($this->success);
		}else{
			return response($this->error);
		};
	}
}
?>
