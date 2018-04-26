<?php
namespace App\Repositories;

use App\ActivityTicketTransferRecord;

class ActivityTicketTransferRecordRepository
{

    protected $activityTicketTransferRecord;

    function __construct(ActivityTicketTransferRecord $activityTicketTransferRecord)
    {
        $this->activityTicketTransferRecord = $activityTicketTransferRecord;
    }

    function create_record($transfer_by, $transfer_to, $ticket_uid){
        return $this->activityTicketTransferRecord->create([
            'transfer_by_id' => $transfer_by,
            'transfer_to_id' => $transfer_to,
            'ticket_uid' => $ticket_uid,
            'success' => true
        ]);
    }
}
?>