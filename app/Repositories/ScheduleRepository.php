<?php

namespace App\Repositories;

use App\AccessAPI;
use App\UserSchedule;

class ScheduleRepository
{

    protected $accessApi;
    protected $userSchedule;

    function __construct(AccessAPI $accessApi, UserSchedule $userSchedule) {
        $this->accessApi = $accessApi;
        $this->userSchedule = $userSchedule;
    }

    function get_schedule($_id, $user_id) {
        $data = array(
            "service" => "get_schedule",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "user_id" => $user_id
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function add_eventblock($_id, $user_id, $date_start, $date_end, $time_start, $time_end, $topic=NULL, $sub_title=NULL, $locked_by_guide=false, $locked_by_tourist=false, $temp_region=NULL, $description=NULL, $tel_number=NULL, $address=NULL, $media=NULL) {
        $data = array(
            "service" => "add_eventblock",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "user_id" => $user_id,
                "date_start" => $date_start,
                "date_end" => $date_end,
                "time_start" => $time_start,
                "time_end" => $time_end,
                "topic" => $topic,
                "sub_title" => $sub_title,
                "locked_by_guide" => $locked_by_guide,
                "locked_by_tourist" => $locked_by_tourist,
                "temp_region" => $temp_region,
                "description" => $description,
                "tel_number" => $tel_number,
                "address" => $address,
                "media" => $media,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function update_eventblock($_id, $id, $user_id, $date_start=NULL, $date_end=NULL, $time_start=NULL, $time_end=NULL, $topic=NULL, $sub_title=NULL, $temp_region=NULL, $description=NULL, $tel_number=NULL, $address=NULL, $media=NULL) {
        $data = array(
            "service" => "update_eventblock",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "id" => $id,
                "user_id" => $user_id,
            )
        );
        if (!is_null($date_start)) {
            # date_start 及 date_end 要成對提供
            $data['data']['date_start'] = $date_start;
            $data['data']['date_end'] = $date_end;
        }
        if (!is_null($time_start)) {
            # time_start 及 time_end 要成對提供
            $data['data']['time_start'] = $time_start;
            $data['data']['time_end'] = $time_end;
        }
        if (!is_null($topic)) {
            $data['data']['topic'] = $topic;
        }
        if (!is_null($sub_title)) {
            $data['data']['sub_title'] = $sub_title;
        }
        if (!is_null($temp_region)) {
            $data['data']['temp_region'] = $temp_region;
        }
        if (!is_null($description)) {
            $data['data']['description'] = $description;
        }
        if (!is_null($tel_number)) {
            $data['data']['tel_number'] = $tel_number;
        }
        if (!is_null($address)) {
            $data['data']['address'] = $address;
        }
        if (!is_null($media)) {
            $data['data']['media'] = $media;
        }
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function del_eventblock($_id, $id, $user_id) {
        $data = array(
            "service" => "del_eventblock",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "id" => $id,
                "user_id" => $user_id,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function lock_eventblock($_id, $id, $user_id) {
        $data = array(
            "service" => "lock_eventblock",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "id" => $id,
                "user_id" => $user_id,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function unlock_eventblock($_id, $id, $user_id) {
        $data = array(
            "service" => "unlock_eventblock",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "id" => $id,
                "user_id" => $user_id,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function create_schedule($guide_id, $tourist_id, $dates) {
        $data = array(
            "service" => "create_schedule",
            "hostname" => "mongo.pneko",
            "data" => array(
                "guide_id" => $guide_id,
                "tourist_id" => $tourist_id,
                "dates" => $dates,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function delete_date($_id, $user_id, $date) {
        $data = array(
            "service" => "delete_date",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "user_id" => $user_id,
                "date" => $date,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function delete_all_eventblocks_in_date($_id, $user_id, $date) {
        $data = array(
            "service" => "delete_all_eventblocks_in_date",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "user_id" => $user_id,
                "date" => $date,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function delete_date_with_all_eventblocks($_id, $user_id, $date) {
        $data = array(
            "service" => "delete_date_with_all_eventblocks",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "user_id" => $user_id,
                "date" => $date,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function unlock_all_eventblocks_in_date($_id, $user_id, $date) {
        $data = array(
            "service" => "unlock_all_eventblocks_in_date",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "user_id" => $user_id,
                "date" => $date,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function add_date($_id, $user_id, $date) {
        $data = array(
            "service" => "add_date",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "user_id" => $user_id,
                "date" => $date,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function get_eventblock($_id, $id, $user_id) {
        $data = array(
            "service" => "get_eventblock",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "id" => $id,
                "user_id" => $user_id,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    function edit_date_with_eventblock($_id, $user_id, $original_date, $new_date) {
        $data = array(
            "service" => "edit_date_with_eventblock",
            "hostname" => "mongo.pneko",
            "data" => array(
                "_id" => $_id,
                "user_id" => $user_id,
                "original_date" => $original_date,
                "new_date" => $new_date,
            )
        );
        $result = $this->accessApi->accessApi($data);
        return($result);
    }

    /*
    *   mysql
    */

    function user_add_schedule($schedule_id, $user_id) {
        $query = $this->userSchedule;
        $query->schedule_id = $schedule_id;
        $query->owner_id = $user_id;
        $query->save();

        if($query) return ['success' => true];
        return ['success' => false];
    }

    function get_all_schedule_by_user_id($user_id) {
        $query = $this->userSchedule->where('owner_id', $user_id)->get();

        if(!$query) return ['success' => false];

        return ['success' => true, 'data' => $query];
    }
}
