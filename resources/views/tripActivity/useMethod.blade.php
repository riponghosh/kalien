<section class="use-method-section">
    <p class="trip-activity-section-title h3" style="font-weight: 400">使用方法</p>
    @if($trip_activity['tel'] != null && $trip_activity['tel_area_code'] != null)
        <p class="h4" style="font-weight: 600">聯絡方式：</p>
        <ul class="ul_list_item_dot">
            <li><span>聯絡電話：</span><span>{{$trip_activity['tel_area_code']}} - {{$trip_activity['tel']}}</span></li>
        </ul>
    @endif
    @if($trip_activity['map_address'] != null)
        <p class="h4" style="font-weight: 600">前往方法：</p>
        <ul class="ul_list_item_dot">
            <li><span>地址：</span><span>{{$trip_activity['map_address']}}</span></li>
        </ul>
    @endif
    @if($trip_activity['open_time'] != null && $trip_activity['close_time'] != null)
        <p class="h4" style="font-weight: 600">營業時間：</p>
        <ul class="ul_list_item_dot">
            <li><span>每天：</span><span>{{date('H:i', strtotime($trip_activity['open_time']))}} ~ {{date('H:i', strtotime($trip_activity['close_time']))}}</span></li>
        </ul>
    @endif
</section>