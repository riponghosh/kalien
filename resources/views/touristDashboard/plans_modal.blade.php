@inject('DatePresenter','App\Presenters\DatePresenter')
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
    </button>
    <p class="h4 modal-title">活動與行程</p>
</div>
@if(count($current_user_requests) > 0 || count($user_gp_activities) > 0)
<div class="modal-body">
    @if(count($user_gp_activities) > 0)
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th>活動名稱</th>
                <th>發起人</th>
                <th>開始日期</th>
                <th>開始時間</th>
                <th>人數</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($user_gp_activities as $user_gp_activity)
                <tr>
                    <td>{{$user_gp_activity['trip_activity']['title_zh_tw']}}</td>
                    <td>
                        <?php $host = $user_gp_activity['host_id'] == Auth::user()->id ? 'self' : $user_gp_activity['host']['name'];?>
                        @if($host == 'self')
                            <label class="label label-info">自己</label>
                        @else
                                {{$host}}
                        @endif
                    </td>
                    <td>{{$user_gp_activity['start_date']}}</td>
                    <td> {{$user_gp_activity['start_time']}}</td>
                    <?php $limit_joiner = $user_gp_activity['limit_joiner'] == null ? '不限' : $user_gp_activity['limit_joiner'];  ?>
                    <td>{{count($user_gp_activity->applicants)}}/{{$limit_joiner}}</td>
                    <td><a href="{{url('/group_events/'.$user_gp_activity['gp_activity_id'])}}" class="btn btn-xs btn-success">打開</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @if(count($current_user_requests)>0)
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th>伙伴</th>
                <th>起始日期</th>
                <th>結束日期</th>
                <th>留言</th>
                <th>狀態</th>
            </tr>
            </thead>
            <tbody>
            @foreach($current_user_requests as $guide)
                @if(isset($guide->userTourist))
                    <?php $app_dates = [];?>
                    @foreach($guide->appointmentDates as $app_date)
                        <?php
                        array_push($app_dates,$app_date['date']);
                        ?>
                    @endforeach
                    <?php $date = $DatePresenter->get_min_and_max_date($app_dates);?>
                <tr id="tourist_row_{{$guide->guide_id}}">
                    <td>
                        {{$guide->userGuide->name}}
                    </td>
                    <td>
                        {{$date['min_date']}}
                    </td>
                    <td>
                        {{$date['max_date']}}
                    </td>
                    <td>
                        @if($guide->msg)
                            <a data-toggle="tooltip" title="Reply !" data-roomId="{{$guide->room_id}}">{{$guide->msg}} <i class='fa fa-comment-o aria-hidden="true'></i></a>
                        @else
                            no new comment.
                        @endif
                    </td>
                    <td>
                        @if($guide->status ==1)
                            <a href="{{url('GET/scheduleDesk/'.$guide->schedule_id)}}"  class="btn btn-success">Open Scuedule</a>
                            <button class="discard_trip_appointment_btn btn btn-icon btn-danger" data-appointment_id = '{{$guide->id}}'><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        @elseif($guide->status ==2)
                            <a class="cancel-trip-request btn btn-danger" data-appointment_id = '{{$guide->id}}' data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing">取消請求</a>
                        @endif
                    </td>
                </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div><!--/modal body-->
@else
<div style="text-align: center; padding: 20px;">
    No any request.
</div>
@endif
<script>
$('.cancel-trip-request').click(function(e){
    e.preventDefault();
    var $btn = $(this);
    $btn.button('loading');
    cancelTripAppointment($btn.data('appointment_id')).done(function(data){
        alert('ok');
        $('#ModalForm').find('.modal-content').load('{{url('/GET/plans_modal')}}');
    }).always(function(){
        $btn.button('reset');
    })
})
$('.discard_trip_appointment_btn').click(function(e){
    var $btn = $(this);
    if(confirm("你決定放棄這次行程？") != true) return;
    discardTripAppointment($btn.data('appointment_id')).done(function(res){
        if(res.success == true){
            alert('ok')
            $('#ModalForm').find('.modal-content').load('{{url('/GET/plans_modal')}}');
        }else if(res.success == false){
            alert('fail');
            $('#ModalForm').find('.modal-content').load('{{url('/GET/plans_modal')}}');
        }
    })
})
</script>
