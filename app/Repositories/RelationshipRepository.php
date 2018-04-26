<?php

namespace App\Repositories;

use App\Relationship;
use App\Repositories\ErrorLogRepository;

class RelationshipRepository
{
	protected $relationship;
	protected $err_log;
	const CLASS_NAME = 'RelationshipRepository';
	const STATUS_IS_FRIEND = 1;

	public function __construct(Relationship $relationship, ErrorLogRepository $errorLogRepository){
		$this->err_log = $errorLogRepository;
		$this->relationship = $relationship;
	}

	public function update_from_notFriend_to_pending($sent_by_id,$sent_to_id){
		if($sent_by_id == $sent_to_id) return false;
		//Set one_id < two_id
		$user_id = $this->sort_user_id($sent_by_id,$sent_to_id);
		if($this->check_relationship_exist($sent_by_id,$sent_to_id)) return false;
		$relationship = $this->relationship;
		$relationship->user_one_id = $user_id['1'];
		$relationship->user_two_id = $user_id['2'];
		$relationship->status 	   = 0;
		$relationship->action_id   = (int)$sent_by_id;
		return $relationship->save();
	}
	public function update_status_to_pending_or_isFriend($sent_by_id,$send_to_id){
		if($sent_by_id == $send_to_id){
            $this->err_log->err('same id', self::CLASS_NAME, __FUNCTION__);
			return false;
        }
		$user_id = $this->sort_user_id($sent_by_id,$send_to_id);
		//Set one_id < two_id
		$query = $this->get_relationship_status($sent_by_id,$send_to_id);

		/*none->pending*/
		if(!$query){
			if( $this->update_from_notFriend_to_pending($sent_by_id,$send_to_id) ) return 'pending';
            $this->err_log->err('update_from_notFriend_to_pending', self::CLASS_NAME, __FUNCTION__);
			return false;
		}
        /*A & B is friend*/
        if($query->status == 1) return 'isFriend';

		/*B pending;A accept*/
		if($query->status == 0 && $query->action_id == $send_to_id){
			if($this->update_status_to_isFriend($sent_by_id,$send_to_id))return 'isFriend';
            $this->err_log->err('update_status_to_isFriend', self::CLASS_NAME, __FUNCTION__);
			return false;
		}
		/*Rejected by current -> accept*/
		if($query->status == 2 && $query->action_id == $sent_by_id){
			if($this->update_status_to_isFriend($sent_by_id,$send_to_id))return 'isFriend';
            $this->err_log->err('update_status_to_isFriend', self::CLASS_NAME, __FUNCTION__);
			return false;
		}
		/*Already pending Or is rejected*/
		if(($query->status == 0 && $query->action_id == $sent_by_id) || ($query->status == 2 && $query->action_id == $send_to_id))return 'pending';
		/*everything else*/
        $this->err_log->err('everything else', self::CLASS_NAME, __FUNCTION__);
		return false;

	}
    public function update_status_to_isFriend($sent_by_id,$sent_to_id){
        if($sent_by_id == $sent_to_id) return false;
        $user_id = $this->sort_user_id($sent_by_id,$sent_to_id);
        /*是否已是朋友*/
        $query = $this->relationship
            ->where([
                ['user_one_id',$user_id['1']],
                ['user_two_id',$user_id['2']],
				['status', 1]
            ])->first();
        if($query) return true;
        $update_to_is_friend_query = $this->relationship
            ->where([
                ['user_one_id',$user_id['1']],
                ['user_two_id',$user_id['2']],
            ])
            ->where(function($query)use($sent_to_id,$sent_by_id){
                $query->where([
                    ['action_id',$sent_to_id],
                    ['status',0]
                ])
                ->orWhere([
                    ['action_id',$sent_by_id],
                    ['status',2]
                ]);
            })
            ->update(['action_id' => (int)$sent_by_id,'status' => 1]);
        if(!$update_to_is_friend_query){
            $this->err_log->err('update to is_Friend failed', self::CLASS_NAME, __FUNCTION__);
        	return false;
		}
        $this->err_log->err('true', self::CLASS_NAME, __FUNCTION__);
		return true;

    }
    public function reject_friend_request($sent_by_id,$sent_to_id){
        if($sent_by_id == $sent_to_id) return false;
        $user_id = $this->sort_user_id($sent_by_id,$sent_to_id);
        return $this->relationship
            ->where([
                ['user_one_id',$user_id['1']],
                ['user_two_id',$user_id['2']],
            ])
            ->where([
                ['action_id',$sent_to_id],
                ['status',0]
            ])
            ->update(['action_id' => (int)$sent_by_id,'status' => 2]);

    }
    public function delete_relationship($sent_by_id,$send_to_id){
    	if($sent_by_id == $send_to_id) return false;
        $user_id = $this->sort_user_id($sent_by_id,$send_to_id);
        /*檢查relationship是否存在*/
        $query = $this->relationship->where([
            ['user_one_id',$user_id['1']],
            ['user_two_id',$user_id['2']],
        ])->first();
        if(!$query)return true;
        return $this->relationship
            ->where([
                ['user_one_id',$user_id['1']],
                ['user_two_id',$user_id['2']]
            ])
			->where(function($query)use($send_to_id,$sent_by_id){
				$query->where([
                    ['action_id',$sent_by_id],
                    ['status',0]
				])->orWhere([
                    ['action_id',$send_to_id],
                    ['status',2]
				])->orWhere([
					['status',1]
				]);
			})
            ->delete();
    }
	public function get_friend_list($self_id){
		$query = $this->relationship
					  ->where('status',1)
			 		  ->where(function ($query) use ($self_id){
			 		  	$query->where('user_one_id',(int)$self_id)
			 		  		  ->orwhere('user_two_id',(int)$self_id);
			 		  })
			 		  ->get();
		return $query;

	}

	public function get_pending_list($self_id){
		$query = $this->relationship
					  ->where('status',0)
					  ->where('action_id','!=',(int)$self_id)
			 		  ->where(function ($query) use ($self_id){
			 		  	$query->where('user_one_id',(int)$self_id)
			 		  		  ->orwhere('user_two_id',(int)$self_id);
			 		  })
			 		  ->get();
		return $query;

	}

	/*
	**取得兩user間的status.
	*/
	public function get_relationship_status($self_id,$user_id){
		$user_id = $this->sort_user_id($self_id,$user_id);

		return $this->relationship
				   	  ->where('user_one_id',$user_id['1'])
					  ->where('user_two_id',$user_id['2'])
				 	  ->first();

	}

	public function check_relationship_exist($sent_by_id,$sent_to_id){
		$user_id = $this->sort_user_id($sent_by_id,$sent_to_id);

		$query = $this->relationship
				   	  ->where('user_one_id',$user_id['1'])
					  ->where('user_two_id',$user_id['2'])
				 	  ->first();

		//data exist ? true : false
		return $query;

	}
	/*
	**user_ids 由小至大排列，sql不用查詢兩次
	*/
	public function sort_user_id($first_id,$second_id){
		if( (int)$first_id < (int)$second_id ){
			$user_one_id = (int)$first_id;
			$user_two_id = (int)$second_id;
		}else{
			$user_one_id = (int)$second_id;
			$user_two_id = (int)$first_id;
		}
		return array('1' => $user_one_id,'2' =>$user_two_id);
	}

}
?>

