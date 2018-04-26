@inject('TripActivityPresenter','App\Presenters\TripActivityPresenter')
@foreach($trip_activity['trip_activity_tickets'] as $trip_activity_ticket)
<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">名稱：{{$TripActivityPresenter->lan_convert($trip_activity_ticket,'name')}}</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>價格</th>
                        <th>折價券</th>
                        <th>票券長度</th>
                        <th>團人數上/下限</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{$trip_activity_ticket['currency_unit']}} {{$trip_activity_ticket['amount']}}</td>
                        <td>{{$trip_activity_ticket['ta_ticket_incidental_coupon']['amount_unit']}} {{$trip_activity_ticket['ta_ticket_incidental_coupon']['amount']}}</td>
                        <td>{{$trip_activity_ticket['qty_unit']}}  {{$trip_activity_ticket['qty_unit_type']}} </td>
                        <td>{{$trip_activity_ticket['max_participant_for_gp_activity']}}/{{$trip_activity_ticket['min_participant_for_gp_activity']}}</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <h4 class="header-title m-t-0 m-b-30">限定場次：
                @if($trip_activity_ticket['has_time_ranges'])
                    <span class="label label-success">有啟用</span>
                @else
                    <span class="label label-primary">無啟用</span>
                @endif
            </h4>
            <div class="text-left">
                <p class="text-muted">
                    <strong>每日時段限制場數:</strong>
                    <span class="m-l-5">{{$trip_activity_ticket['time_range_restrict_group_num_per_day']}}</span>
                </p>
                <p class="text-muted">
                    <strong>起始與最終場次:</strong>
                    <span class="m-l-5">{{$trip_activity_ticket['fix_time_ranges']['start_time']}} - {{$trip_activity_ticket['fix_time_ranges']['final_time']}}</span>
                </p><!--end text-muted -->
                <p class="text-muted">
                    <strong>每場區間:</strong>
                    <span class="m-l-5">{{$trip_activity_ticket['fix_time_ranges']['interval']}}{{$trip_activity_ticket['fix_time_ranges']['interval_unit']}}</span>
                </p><!--end text-muted -->
            </div>
        </div>
    </div>
</div>
@endforeach