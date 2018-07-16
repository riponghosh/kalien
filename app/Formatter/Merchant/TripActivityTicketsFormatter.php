<?php
namespace  App\Formatter\Merchant;

use App\Formatter\Interfaces\IFormatter;
use App\Formatter\TripActivity\PackageDisableDatesFormatter;
use App\Formatter\TripActivity\PackageDisableWeekFormatter;

class TripActivityTicketsFormatter implements IFormatter
{
    /*
     * 範例
        "id": 4,
        "name_zh_tw": "2小時",
        "description_zh_tw": "專人教練",
        "amount": 500,
        "currency_unit": "TWD",
        "qty_unit": "2",
        "trip_activity_id": 7,
        "merchant_id": 3,
        "created_at": null,
        "updated_at": null,
        "deleted_at": null,
        "restrict_qty_per_day": null,
        "available": 1,
        "qty_unit_type": "hour",
        "min_participant_for_gp_activity": 5,
        "max_participant_for_gp_activity": 14,
        "has_time_ranges": 1,
        "time_range_restrict_group_num_per_day": 1
     */
    protected $ticketIncidentalCouponsFormatter;
    protected $packageDisableDatesFormatter;
    protected $packageDisableWeekFormatter;

    public function __construct(TicketIncidentalCouponsFormatter $ticketIncidentalCouponsFormatter, PackageDisableDatesFormatter $packageDisableDatesFormatter, PackageDisableWeekFormatter $packageDisableWeekFormatter)
    {
        $this->ticketIncidentalCouponsFormatter = $ticketIncidentalCouponsFormatter;
        $this->packageDisableDatesFormatter = $packageDisableDatesFormatter;
        $this->packageDisableWeekFormatter = $packageDisableWeekFormatter;
    }

    public function dataFormat($data, callable $closure = null)
    {
        // TODO: Implement dataFormat() method.
        if(!$data)
        {
            return [];
        }

        return $data->map(function($tripActivityTicket){
            $tripActivityTicket = $this->activity_language_convert($tripActivityTicket, 'zh_tw');
            return (object) [
                "id" => $tripActivityTicket->id,
                "name" => $tripActivityTicket->name,
                "amount" => $tripActivityTicket->amount,
                "currency_unit" => $tripActivityTicket->currency_unit,
                "qty_unit" => $tripActivityTicket->qty_unit,
                "qty_unit_type" => $tripActivityTicket->qty_unit_type,
                "min_participant_for_gp_activity" => $tripActivityTicket->min_participant_for_gp_activity,
                "max_participant_for_gp_activity" => $tripActivityTicket->max_participant_for_gp_activity,
                "has_time_ranges" => $tripActivityTicket->has_time_ranges,
                "time_range_restrict_group_num_per_day" => $tripActivityTicket->time_range_restrict_group_num_per_day,
                "ta_ticket_incidental_coupon" => $this->ticketIncidentalCouponsFormatter->dataFormat($tripActivityTicket->ta_ticket_incidental_coupon),
                "disable_sales_dates" => $this->packageDisableDatesFormatter->dataFormat($tripActivityTicket->disable_dates),
                "disable_sales_weeks" => $this->packageDisableWeekFormatter->dataFormat($tripActivityTicket->disable_weeks),
                'gp_buying_status' => $tripActivityTicket->gp_buying_status
            ];
        });
    }

    public function activity_language_convert($data, $language){
        $lan = ['zh_tw', 'jp', 'en'];
        $trans_data = ['name','sub_title','description','map_address'];
        if(!in_array($language, $lan)) return false;
        foreach ($trans_data as $k){
            if($data{$k.'_'.$language} != null){
                $data{$k} = $data{$k.'_'.$language};
            }else{
                //取得唯一語言
                $lan_detect = $lan;
                foreach ($lan_detect as $det_lan){
                    if($data{$k.'_'.$det_lan} != null){
                        $data{$k} = $data{$k.'_'.$det_lan};
                        break ;
                    }
                    if(end($lan_detect) === $det_lan){
                        $data{$k} = null;
                    }
                }
            }


        }
        return $data;
    }
}