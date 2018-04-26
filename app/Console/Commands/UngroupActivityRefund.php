<?php

namespace App\Console\Commands;

use App\Services\UserActivityTicket\ActivityTicketService;
use Illuminate\Console\Command;
use App\Services\UserGroupActivityService;
use DB;
use File;
use League\Flysystem\Exception;

class UngroupActivityRefund extends Command
{
    // 命令名稱
    protected $signature = 'invalidGroupRefund';

    // 說明文字
    protected $description = '退票給未成團的活動。';


    protected $userGroupActivityService;
    protected $userActivityTicketService;
    public function __construct(UserGroupActivityService $userGroupActivityService, ActivityTicketService $userActivityTicketService)
    {
        $this->userActivityTicketService = $userActivityTicketService;
        $this->userGroupActivityService = $userGroupActivityService;
        parent::__construct();
    }


    public function handle()
    {
        $log_info = array(
            'task_name' => 'invalidGroupRefund',
            'msg' => 'exc'
        );
        //任務有被啟動
        $this->write_log($log_info);
        $gp_activities = $this->userGroupActivityService->get_group_activities([
            'need_min_joiner_for_avl_gp' => true,
            'over_start_at' => true,
            'is_available_group_for_limit_gp_ticket' => false
        ]);
        $gps_id = array_pluck($gp_activities, 'id');
        $log_info = array(
            'task_name' => 'invalidGroupRefund',
            'msg' => 'get all gp',
            'err' => [],
            'data' => [
                'gps_id' => json_encode($gps_id, true),
                'tickets' => [],
                'tickets_amt' => 0
            ],

        );
        $refund_tickets_id = array();
        foreach ($gp_activities as $gp_activity){
            foreach ($gp_activity->relate_user_activity_tickets as $relate_user_activity_ticket){
                if(isset($relate_user_activity_ticket->user_activity_ticket)){
                    $tic_info = [
                        'tic_id' => $relate_user_activity_ticket->user_activity_ticket->id,
                        'refund_success' => false
                    ];
                    try{
                        $ticket_uid = $relate_user_activity_ticket->user_activity_ticket->id;
                        array_push($refund_tickets_id, $ticket_uid);
                        $refund = $this->userActivityTicketService->refund_by_ticket_id($relate_user_activity_ticket->user_activity_ticket->ticket_id,[
                            'is_min_joiner_gp_and_not_avl' => true
                        ]);
                        if($refund){
                            $tic_info['refund_success'] = true;
                        }
                    } catch (Exception $e){
                        array_push($log_info['err'], ['tic_id' => $ticket_uid,'err' => $e]);
                    }
                    array_push($log_info['data']['tickets'], $tic_info);

                }
            }
            $log_info['data']['tickets_amt'] = count($log_info['data']['tickets_amt']);
        }

        $this->write_log($log_info);

    }

    public function write_log($log_info = array()){
        $log_info['date'] = date('Y-m-d H:i:s');
        // 記錄 JSON 字串
        $log_info_json = json_encode($log_info) . "\r\n";

        // 記錄 Log
        File::append( storage_path('/logs/ScheduleTask.log'), $log_info_json);
    }
}