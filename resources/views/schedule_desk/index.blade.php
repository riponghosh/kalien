@inject('MediaPresenter','App\Presenters\MediaPresenter')
@extends('layouts.schedule')
@section('title')
Pneko schedule
@endsection
@section('content')
@inject('DatePresenter','App\Presenters\DatePresenter')
<div class="editor-container">
  <div class="schedule-container">
    <div class="tools-container">
      <ul>
        <li class="add_date_btn tools-btn">
          <i class="fa fa-calendar-plus-o fa-lg"></i>
        </li>
      </ul> 
    </div>
    <div class="schedule unselectable">
      <div class="timeline">
        <div class="th">
          <ul class="ctrl_btn page_control_btns_group list-inline m-l-0 p-t-5" style="font-size: 1rem;">
          <li class="ctrl_btn left_btn"><i class="fa fa-arrow-left fa-1" aria-hidden="true"></i></li>
          <li class="ctrl_btn right_btn"><i class="fa fa-arrow-right" aria-hidden="true"></i></li>
          </ul>
        </div>

        <div class="content">
        </div>
      </div>
      <div class="relative_area"></div>
    </div>
    <div class="eventBlock-temporary-container">
    </div>
  </div>
  <div class="list-container">
  <div class="list-header" >
    <div class="blockLock_container pull-right">
      <i class="o_locked_msg" style="color:red;display: none; margin-right: 3px">對方鎖上行程</i>
      <div class="lock_btn" style="display: inline-block"></div>
    </div>
  </div>
  <div class="list-body">
    <div class="basicInfo_section">
      <ul>
        <li class="topic unfill">
          <input type="text" value="" name="topic"/>
        </li>
        <li class="location unfill">
          <input type="text" value="" name="sub_title"/>
        </li>
        <!--
        <li class="time-preiod">
            <div class="start-time">
              <div class="sub-title">開始
              </div>
              <div class="time-btn">
                <input type="text" class="time" value="9:30" /><span class="caret"></span>
              </div>
              <div class="date-btn">
                <input type="text" class="date" value="16-SEP-2016"></input>
                <span class="caret"></span>
              </div>
            </div>
            <div class="end-time">
              <div class="sub-title">結束
              </div>
              <div class="time-btn">
                <input type="text" class="time" value="11:30" ></input>
                <span class="caret"></span>
              </div>
              <div class="date-btn">
                <input type="text" class="date" value="16-SEP-2016"><span class="caret"></span></input>
              </div>
            </div>
        </li> -->
        <li class="notes">
          <span>備註</span>
          <textarea class="description" name="description"></textarea>
        </li>
      </ul>
    </div>
    <!--
    <div class="photos_section">
      <p>圖片</p>
      <div class="photos_group_container">
        <div class="photo-thumbnail m-b-10">
          <div class="photo-thumbnail-preview">
            <div class="open-menu-btn">
              <i class="fa fa-ellipsis-v fa-lg"></i>
            </div>
          </div>
          <div class="photo-description">

          </div>
        </div>
      </div>
    </div>
    -->
  </div>
  <div class="list-footer">
    <div class="delete">
      <input type="button" class="delete-eventBlock" disabled value="刪除行程"/>
    </div>
  </div>
  </div>
</div>
  <div id="chat_rooms"></div>
<script>
    Notification.TOKEN = '{{sha1(Auth::user()->socket_token.'add_salt')}}';
  $(function(){
    @if($chatroom_id != null)
    <?php
    if(isset(Auth::user()->user_icons[0])){
        $current_user_icon_path = $MediaPresenter->img_path(Auth::user()->user_icons[0]->media->media_location_low);
    }else{
        $current_user_icon_path = null;
    }
    if(isset($other_user->user_icons[0])){
       $other_user_icon_path = $MediaPresenter->img_path($other_user->user_icons[0]->media->media_location_low);
    }else{
        $other_user_icon_path = null;
    }
   ?>
    roomsManager.initialize($('#chat_rooms'), {{Auth::user()->id}});
    roomsManager.create({{$chatroom_id}}, {
        id: {{Auth::user()->id}},
        profilePic:'{{$current_user_icon_path}}'
    },{
        id: {{$other_user->id}},
        name: '{{$other_user->name}}',
        profilePic: '{{str_replace('\n','',$other_user_icon_path)}}'
    });
    @endif
    <?php
        $dates = [];
        foreach ($schedule->dates as $date){
          array_push($dates, strtotime($date['date']));
        }
    ?>
    var schedule = schedularsManager.create(
        '{{$schedule->_id}}', $('.schedule'), 8, 24,
        {{Auth::user()->id == $schedule->tourist_id ? 1 : 0}}
    );
    schedule.setBasicData({!!json_encode($dates) !!}, [ // TODO strtotime
      @foreach($schedule->event_blocks as $event_block)
        {
          id:'{{$event_block['id']}}', 
          topic: '{{$event_block['topic']}}',
          sub_title: '{{$event_block['sub_title']}}',
          description: '{{ str_replace("\n","\\n" ,$event_block['description'])}}',
          date: '{{$event_block['date_start']}}',
          from: {{$DatePresenter->time_to_min($event_block['time_start']) }},
          to: {{$DatePresenter->time_to_min($event_block['time_end']) }},
        @if(Auth::user()->id == $schedule->tourist_id)
          s_locked: {{var_export($event_block['locked_by_tourist'],true)}},
          o_locked: {{var_export($event_block['locked_by_guide'],true)}}
        @elseif(Auth::user()->id == $schedule->guide_id)
          s_locked: {{var_export($event_block['locked_by_guide'],true)}},
          o_locked: {{var_export($event_block['locked_by_tourist'],true)}}
        @endif

        },
      @endforeach
    ]); //TODO PHP data
    $('.add_date_btn').click(function(){
        schedule.addDate1();
    })
  });
</script>
@endsection
