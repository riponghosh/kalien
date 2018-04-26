<a href="{{url('/group_events/'.$group_activity->gp_activity_id)}}" target="_blank">
<div class="groupActivityCard grid-item" style="width: 300px;" data-gpactivity-id={{$group_activity->gp_activity_id}}>
    <div class="groupActivityCard-header">
        <div class="media-topic-tag omit_text">
            {{$group_activity->activity_title}}
        </div>
        <div class="media_wrapper">
            <img class="img-fit-cover" src="{{Storage::url(optional(optional($group_activity->trip_activity->trip_activity_cover)->media)->media_location_low)}}">
        </div>
    </div><!--header row-->
    <div class="groupActivityCard-body" style="padding-left: 15px;">
        <div class="head-row" style="height: 50px;">
            <div class="userCircle">
                @if(isset($group_activity['host']['user_icon'][0]) )
                    @if (file_exists(public_path(Storage::url($group_activity->host->user_icon[0]->media->media_location_low))))
                        <img class="userImg img-circle" src="{{Storage::url($group_activity->host->user_icon[0]->media->media_location_low)}}"/>
                    @else
                        <img class="userImg img-circle" src="/img/icon/user_icon_bg.png"/>
                    @endif
                @else
                    <img class="userImg img-circle" src="/img/icon/user_icon_bg.png"/>
                @endif
            </div>
            <div class="title-wrapper col-xs-offset-4">
                <div class="row m-t-10">
                    <div class="name_container col-xs-6">
                        <span style="display: block;font-size:12px;color: #c1c1c1;">發起人</span>
                        <span style="display: block;color: #fff;">{{$group_activity->host->name}}</span>
                    </div>
                    <div class="name_container col-xs-6">
                        <span style="display: block ;font-size:12px;color: #c1c1c1;">時數</span>
                        <span style="display: block;color: #fff;">{{$group_activity->duration}} {{$group_activity->duration_unit}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <p class="col-xs-12" style="font-size: 18px;color: #fff;">
                {{$group_activity->trip_activity->title_zh_tw}}
            </p>
        </div>
        <div class="row p-b-10">
            <div class="col-xs-12" style="color: #c1c1c1;">
                <i class="fa fa-map-marker m-r-5"></i>{{$group_activity->trip_activity->map_address_zh_tw}}
            </div>
        </div>
        <div class="row p-b-10">
            <div class="col-xs-12" style="color: #c1c1c1;">
                <i class="fa fa-clock-o m-r-5"></i>{{date('H:i',strtotime($group_activity->start_time))}}
            </div>
        </div>
        <div class="row p-b-15">
            <div class="col-xs-12" style="color: #c1c1c1;">
                <?php $limit_joiner = $group_activity->limit_joiner == null ? '不限' : $group_activity->limit_joiner;  ?>
                <span style="display: block; padding-bottom: 2px;">人數({{count($group_activity->applicants)}}/{{$limit_joiner}})</span>
                @if(count($group_activity->applicants) > 0)
                <div class="applicant-icon-group icon-pond">
                    <div class="ooo">
                        @foreach($group_activity->applicants as $applicant)
                        <?php $applicant_icons = isset($applicant->user->user_icon[0]) ? Storage::url($applicant->user->user_icon[0]['media']['media_location_standard']) : '/img/icon/user_icon_bg.png'?>
                        <div class="o o-black" style="--bgi:url('{{$applicant_icons}}')">
                        </div>
                        @endforeach
                    </div>
                </div><!--end icon-pond-->
                @endif
            </div>
        </div>
    </div>
    <div class="groupActivityCard-footer">
        <div>
            <div class="footer-btn col-xs-6 start-date-btn" style="border-right: 1px #343538 solid;border-top: 4px solid #e64c65;">
                <span><i class="fa fa-calendar"></i></span><span style="float:right">{{$group_activity->start_date}}</span>
            </div>
            <div class="footer-btn col-xs-6 p-l-10" style="border-top: 4px solid #11a8ab;">
                <span>費用</span><span style="float:right">NT$ {{$group_activity->trip_activity_ticket->amount}}</span>
            </div>
        </div>
    </div>
</div>
</a>