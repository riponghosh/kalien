<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\RelationshipRepository;
use App\Services\RelationshipService;
/*
| status   none      :  not in table
|          pending   :  status(0) ,action_id(發起人)
|          is friend :  status(1) ,action_id(被邀請)
|          rejected  :  status(2) ,action_id(被邀請)
|
| method   add       :  [a,b] not in tb;
|                       action=>a , status=>0
|          cancel    :  [a,b] in tb,status=>0,is action_id Or [a,b] in tb,status=>1,is action_id
|                       delete;
|          unfriend  :  [a,b] in tb,status=>1
|                        delete;
|          accept    :  [a,b] in tb,status=>0,is not action_id or status=>2,is action_id
|                        status=>1
|          reject    :  [a,b] in tb,status=>0,is not action_id
|                        status=>2
*/
class RelationshipController extends Controller
{
    protected $relationshipRepository;
    protected $relationshipService;
    protected $success = array();
    protected $error = array();

    public function __construct(RelationshipRepository $relationshipRepository,RelationshipService $relationshipService)
    {
        $this->relationshipRepository = $relationshipRepository;
        $this->relationshipService = $relationshipService;
        $this->success = array('status' => 'success');
        $this->error = array('status' => 'error');

    }

    public function send_friend_request(Request $request){
    	$sent_by_id = Auth::user()->id;
    	$sent_to_id = $request->user_id; //other user.
    	$friend_status = $this->relationshipService->send_friend_request($sent_to_id,$sent_by_id);
        if($friend_status == false) return $this->error;
        $this->success['friend_status'] = $friend_status;
    	return $this->success;
    }

    public function cancel_friend_request(Request $request){
        $sent_by_id = Auth::user()->id; //send request by.
        $sent_to_id = $request->user_id;
        if(!$this->relationshipService->cancel_friend_request($sent_to_id,$sent_by_id)) return $this->error;
        return $this->success;
    }

    public function accept_friend_request(Request $request){
    	$sent_by_id = Auth::user()->id; //send request by.
    	$sent_to_id = $request->user_id;
    	if(!$this->relationshipService->accept_friend_request($sent_to_id,$sent_by_id)) return $this->error;
        return $this->success;
    }
    public function reject_friend_request(Request $request){
        $sent_by_id = Auth::user()->id; //send request by.
        $sent_to_id = $request->user_id;
        if(!$this->relationshipService->reject_friend_request($sent_to_id,$sent_by_id)) return $this->error;
        return $this->success;
    }
    public function unfriend(Request $request){
        $sent_by_id = Auth::user()->id; //send request by.
        $sent_to_id = $request->user_id;
        if(!$this->relationshipService->unfriend($sent_to_id,$sent_by_id)) return $this->error;
        return $this->success;
    }

    public function get_relationship_status($user_id){
    	return $this->relationshipService->get_relationship_status(Auth::user()->id,$second_user_id);
    }

    public function get_freind_list(){
    	$self_id = Auth::user()->id;
    	return $this->relationshipRepository->get_friend_list($self_id);
    }

    public function get_pending_list(){
    	$self_id = Auth::user()->id;
    	return $this->relationshipRepository->get_friend_list($self_id);
    }

}
