<?php

namespace App\Services;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\RelationshipRepository;

class RelationshipService
{
	protected $relationshipRepository;
    
    public function __construct(RelationshipRepository $relationshipRepository)
    {
        $this->relationshipRepository = $relationshipRepository;
    }

    public function send_friend_request($send_to_id,$sent_by_id = NULL){
    	$sent_by_id = $sent_by_id == NULL ? Auth::user()->id : $sent_by_id;
    	return $this->relationshipRepository->update_status_to_pending_or_isFriend($sent_by_id,$send_to_id);
    }

    public function cancel_friend_request($send_to_id,$sent_by_id = NULL){
        $sent_by_id = $sent_by_id == NULL ? Auth::user()->id : $sent_by_id;
        return $this->relationshipRepository->delete_relationship($sent_by_id,$send_to_id);
    }

    public function accept_friend_request($send_to_id,$sent_by_id = NULL){
        $sent_by_id = $sent_by_id == NULL ? Auth::user()->id : $sent_by_id;
    	return $this->relationshipRepository->update_status_to_isFriend($sent_by_id,$send_to_id);
    }

    public function reject_friend_request($send_to_id,$sent_by_id = NULL){
        $sent_by_id = $sent_by_id == NULL ? Auth::user()->id : $sent_by_id;
		return $this->relationshipRepository->reject_friend_request($sent_by_id,$send_to_id);
    }

    public function unfriend($send_to_id,$sent_by_id = NULL){
        $sent_by_id = $sent_by_id == NULL ? Auth::user()->id : $sent_by_id;
		return $this->relationshipRepository->delete_relationship($sent_by_id,$send_to_id);
    }

    public function get_relationship_status($first_user_id,$second_user_id){
    	return $this->relationshipRepository->get_relationship_status($first_user_id,$second_user_id);
    }
}
?>