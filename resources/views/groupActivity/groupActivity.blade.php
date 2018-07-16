@extends('layouts.app')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('TripActivityPresenter','App\Presenters\TripActivityPresenter')
@if($group_activity == null)
    @section('content')
        此活動不存在
    @endsection
@else
@if($group_activity['activity_title'] != null)
@section('meta_title')
{{$group_activity['activity_title']}}
@endsection
@elseif($trip_activity['title'] != null && $trip_activity['sub_title'] != null)
@section('meta_title')
{{$trip_activity['title']}} - {{$trip_activity['sub_title']}}
@endsection
@endif
@section('meta_img')
{{!empty($trip_activity['trip_gallery_pic_low_quality']) ? url(Storage::url($trip_activity['trip_gallery_pic_low_quality'])) : url(Storage::url('components/Pneko_lg.png'))}}
@endsection

@section('header')
    <div class="group-activity-header" style="background-image: url({{$trip_activity['trip_gallery_pic']}})">
        <div class="group-activity-header-bottom-overlay">
        </div>
        <div class="group-activity-header-container">
            <div class="group-activity-header-container-title-wrapper">
                <p class="h1 title">{{$trip_activity['title']}} - {{$trip_activity['sub_title']}}</p>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="group-activity-body" style="max-width: 1200px; margin: 0 auto;">
        <div class="row">
            <div class="left-side col-md-8">
                <div class="group-activity-content-header">
                    <p class="h2">
                        {{$group_activity['activity_title']}}
                    </p>
                </div>
                <div class="group-activity-content-info">
                </div>
                <hr>
                @if(count($trip_activity['rule_infos']) > 0)
                    @include('tripActivity.ruleInfos')
                    <hr>
                @endif
                @if(count($trip_activity['trip_activity_short_intros']) > 0)
                    @include('tripActivity.shortIntros')
                    <hr>
                @endif
                @if(count($trip_activity['media']) > 1)
                    @include('tripActivity.activityPromo')
                    <hr>
                @endif
                @include('tripActivity.useMethod')
                <hr>
                @include('tripActivity.refundInfo')
                <hr>
                @include('tripActivity.customerRight')
            </div>
            <div class="right-side col-md-4">
                <div class="organiserInfo-section">
                    <div class="organiserInfo-section-header">
                        <div class="userCircle m-r-15">
                            <?php $host_avatar = $group_activity['host']['sm_avatar'] ?>
                            @if(!empty($host_avatar))
                                @if (file_exists(public_path($host_avatar)))
                                    <img class="userImg img-circle" src="{{$host_avatar}}"/>
                                @else
                                    <img class="userImg img-circle" src="/img/icon/user_icon_bg.png"/>
                                @endif
                            @else
                                <img class="userImg img-circle" src="/img/icon/user_icon_bg.png"/>
                            @endif
                        </div><!--End userCircle-->
                        <div class="title-wrapper m-t-10">
                                <ul class="list-inline">
                                    <li class="name_container">
                                        <span style="display: block;font-size:12px;color: #c1c1c1;">團長</span>
                                        <span style="font-size: 16px;display: block;">{{$group_activity['host']['name']}}</span>
                                    </li>
                                    <li class="name_container">
                                        <span style="display: block ;font-size:12px;color: #c1c1c1;">時數</span>
                                        <span style="font-size: 16px;display: block;">{{$group_activity['duration']}} {{$group_activity['duration_unit']}}</span>
                                    </li>
                                </ul>
                        </div>
                    </div><!--End organiserInfo-section-header-->
                    <div class="organiserInfo-section-body">
                        <ul>
                            <li class="m-b-10"><i class="fa fa-calendar m-r-5  fa-lg"></i>{{$group_activity['start_date']}}</li>
                            <li><i class="fa fa-clock-o m-r-5 fa-lg"></i> {{date('H:i',strtotime($group_activity['start_time']))}}開始</li>
                        </ul>
                        <div>
                            <?php $limit_joiner = empty($group_activity['limit_joiner']) ? '不限' : $group_activity['limit_joiner'];  ?>
                            <p class="display: block;padding-bottom: 5px;"><span class="m-r-10">人數({{count($group_activity['participants'])}}/{{$limit_joiner}})</span>
                                @if($group_activity['is_achieved'] == true)
                                    <span class="label label-success">成團</span>
                                @elseif($group_activity['forbidden_reason'] == 2)
                                    <span class="label label-default">未達成團人數</span>
                                @elseif($group_activity['forbidden_reason'] == 4)
                                    <span class="label label-danger">成團失敗</span>
                                @elseif($group_activity['forbidden_reason'] == 5)
                                    <span class="label label-default">購票處理中</span>
                                @else
                                    <span class="label label-danger">成團失敗</span>
                                @endif
                            </p>
                            @if(count($group_activity['participants']) > 0)
                                @foreach($group_activity['participants'] as $participant)
                                    <?php $participant_avatar = !empty($participant['sm_avatar']) ? $participant['sm_avatar'] : '/img/icon/user_icon_bg.png'?>
                                    <div class="d-inline-block m-r-5 m-b-5" data-toggle="tooltip" title="{{$participant['name']}}" style="width: 30px;height: 30px;">
                                        <img class="img-circle" src="{{$participant_avatar}}" data-toggle="tooltip" style="width: 100%; height: 100%;">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div><!--End organiserInfo-section-body-->
                    <div class="organiserInfo-section-footer">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="font-c-pneko-red" style="font-size: 22px;">
                                    NT$ {{$group_activity['joining_fee']}}
                                </div>
                                <span class="text-muted" style="font-size: 12px">參加費用</span>
                            </div>
                            <div class="col-sm-6">
                                @if(strtotime($group_activity['start_date']) < strtotime(Carbon::today($group_activity['timezone'])) )
                                    <button class="join-gp-and-purhase-btn btn btn-primary" style="width: 100%" data-gpactivity-id={{$group_activity['gp_activity_id']}} disabled>
                                        停止參加
                                    </button>
                                @else
                                    <button class="join-gp-and-purhase-btn btn btn-pneko-red btn-lg" style="width: 100%" data-gpactivity-id={{$group_activity['gp_activity_id']}}>
                                        參加付款
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div><!--End organiserInfo-section-footer-->
                </div>
                <div class="socials-btn-group-section">
                    <p class="h6 text-muted">Share this</p>
                    <button id="FBShareBtn" class="social-circle-btn fb-btn"><i class="fa fa-facebook fa-lg"></i></button>
                </div>
            </div><!-- End Right Side -->
        </div>
    </div>
@endsection

@section('script')
<script>
//reCss Main
$('body').addClass('bg_white');
$('.join-gp-and-purhase-btn').click(function () {
    var gpActivityId = $(this).data('gpactivityId');
    swal({
        title: '參加活動？',
        showCancelButton: true,
        confirmButtonClass: 'btn-success waves-effect waves-light',
        confirmButtonText: '參加並購票',
        closeOnConfirm: false,
    },function () {
        sdReqJoinGpActivity({gpActivityId: gpActivityId}, function () {
            swal.close();
        },function(res,params,err,callback){
            sdReqJoinGpActivityCallback(res,params,err,callback)
        });
    });

});
</script>
@endsection
@endif