<section class="master-rule-info-section">
    <p class="trip-activity-section-title h3" style="font-weight: 400">退款須知</p>
    <p><span class="m-r-5">如果是團體活動，已下退款方式用於</span><span class="label label-success">成團</span></p>
    @if(count($trip_activity['trip_activity_refund_rules']) == 0 && $trip_activity['merchant_id'] != env('MERCHANT_ID_PNEKO'))
        <p>活動開始前一天可退票</p>
    @else
        <ul class="ul_list_item_dot">
        @if($trip_activity['merchant_id'] == env('MERCHANT_ID_PNEKO'))
                <li>購票且成團後不能退款</li>
        @endif
        @foreach($trip_activity['trip_activity_refund_rules'] as $trip_activity_refund_rule)
            <li>
            @if($trip_activity_refund_rule->purchase_any_time)
                購票後
            @elseif($trip_activity_refund_rule->refund_before_day > 0)
                活動開始前<b>{{$trip_activity_refund_rule->refund_before_day}}</b>天內
            @endif
            @if($trip_activity_refund_rule->refund_percentage == 0)
                不能退款
            @else
                只能退回最終售價的<b>{{$trip_activity_refund_rule->refund_percentage}}%</b>
            @endif
        </li>
        @endforeach
        </ul>
    @endif

</section>