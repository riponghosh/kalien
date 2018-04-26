@extends('layouts.app')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('TripActivityPresenter','App\Presenters\TripActivityPresenter')
@section('header')
<div class="trip-activity-header" style="background-image: url({{$MediaPresenter->img_path($trip_activity['trip_gallery_pic'])}})">
    <div class="trip-activity-header-bottom-overlay">
    </div>
    <div class="trip-activity-header-container">
        <div class="trip-activity-header-container-title-wrapper">
            <p class="h1 title">{{$trip_activity['title']}} - {{$trip_activity['sub_title']}}</p>
        </div>
    </div>
</div>
@endsection
@section('content')
<div class="trip-activity-body">
    <div class="left-side">
        @if(count($trip_activity['rule_infos']) > 0)
            @include('tripActivity.ruleInfos')
            <hr>
        @endif
        @if(count($trip_activity['trip_activity_short_intros']) > 0)
            @include('tripActivity.shortIntros')
        <hr>
        @endif
        @if(count($trip_activity_tickets) > 0)
        <section id="price-plan-section" class="price-plan-section">
            <p class="trip-activity-section-title h3" style="font-weight: 400">收費方案</p>
            <ul class="price-plan-list">
                @foreach($trip_activity_tickets as $t_a_ticket)
                <li class="activity-ticket-plan m-b-15" style="background: #fff" data-ticket-id="{{$t_a_ticket->id}}">
                    <div class="ticket-section">
                        <div class="m-b-15 p-t-15 p-b-15">
                            <div class="col-xs-8">
                                {{$t_a_ticket->name}}
                            </div>
                            <div class="col-xs-4 text-right">
                                {{$UserPresenter->cur_units(CLIENT_CUR_UNIT, 's')}} {{cur_convert($t_a_ticket->amount,$t_a_ticket->currency_unit)}}
                            </div>
                        </div>
                        <div style="height:40px;">
                            <div class="col-xs-4 pull-right text-right">
                                <button class="expand-gp-activity-section-btn btn btn-success-outline btn-sm">建立活動</button>
                            </div>
                        </div>
                    </div>
                    <div class="gp-activity-section" data-ticket-id="{{$t_a_ticket->id}}" style="padding: 15px;display: none">
                        <hr>
                        <div class="row">
                            <div class="col-sm-12 form-group">
                                <label class="control-label">活動主題</label>
                                <input name="gp_activity_title" type="text" class="form-control" placeholder="範例：同學們一起聚聚吧!">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label class="control-label">舉辦日期</label>
                                <input name="start_date" type="text" class="hold-date-input form-control" data-mask="9999-99-99">
                            </div>
                            <div class="col-sm-3 form-group">
                                <label class="control-label">開始時間</label>
                                <select type="select" name="start_time" type="text" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 col-xs-6 form-group">
                                <label class="control-label">上限人數</label>
                                <select  name="limitApplicants" class="form-control">
                                    <?php $min_participant = $t_a_ticket->min_participant_for_gp_activity == null ? 1 : $t_a_ticket->min_participant_for_gp_activity;?>
                                    <?php $max_participant = $t_a_ticket->max_participant_for_gp_activity == null ? 20 : $t_a_ticket->max_participant_for_gp_activity;?>
                                    @if($t_a_ticket->max_participant_for_gp_activity == null)
                                        <option value='unlimited'>不限</option>
                                    @endif
                                   @for($i = $min_participant; $i <=$max_participant; $i++)
                                        @if($i == $min_participant)
                                                <option value="{{$i}}">{{$i}} (最少)</option>
                                        @elseif($i == $max_participant)
                                                <option value="{{$i}}">{{$i}}(上限)</option>
                                        @else
                                                <option value="{{$i}}">{{$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <button class="create-gp-activity-btn btn btn-success" style="width: 80%;">建立</button>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </section>
        <hr>
        @endif
        @if(count($trip_activity['trip_img']) > 1)
            @include('tripActivity.activityPromo')
            <hr>
        @endif
        @include('tripActivity.useMethod')
        <hr>
        @include('tripActivity.refundInfo')
        <hr>
        @include('tripActivity.customerRight')
    </div>
</div>
@endsection

@section('script')
<script>
    //reCss Main
    $('body').addClass('bg_white');

    $('.price-plan-section').find('.activity-ticket-plan').each(function () {
        var $container = $(this).find('.gp-activity-section');
        var createGpActivityComponent = createGpActivity($(this).find('.gp-activity-section'));
        $(this).find('.expand-gp-activity-section-btn').click(function () {
            createGpActivityComponent.rawDisableDatesData();
            $(this).closest('.activity-ticket-plan').find('.gp-activity-section').toggle();
        })
        //createGpActivity($(this).find('.expand-gp-activity-section-btn'), $activityTicketId,$gpActivityInfoSection);
    })
</script>
@endsection