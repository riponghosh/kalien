@inject('GuidePreServicePresenter','App\Presenters\GuidePreServicePresenter')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('GuideMatchStatusPresenter','App\Presenters\GuideMatchStatusPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@inject('DatePresenter','App\Presenters\DatePresenter')
<?php $guide = $user->guide ?>
<?php
//guide_match_status  sorting
$gt_match_accept = [];
$gt_match_reject = [];
$gt_match_pending = [];
$gt_match_expired = [];
foreach($match_status as $t_match_status){
    if($t_match_status->is_expired == true){
		array_push($gt_match_expired,$t_match_status);
	}elseif($t_match_status->status == 1){
		array_push($gt_match_accept,$t_match_status);
	}elseif($t_match_status->status == 2){
        array_push($gt_match_pending,$t_match_status);
	}elseif($t_match_status->status == 3){
        array_push($gt_match_reject,$t_match_status);
    }
}
?>
<div class="user_modal-close">
	<i class="fa fa-times fa-2x" aria-hidden="true">
	</i>
</div>
<div class="userProfile container-fluid">
	<div class="row row-no-padding default_block_style m-b-8">
	    <div class="icon-container col-md-3 col-sm-3 col-xs-12">
	      <div class="img-container">
		  @if($user_icon != null)
			  @if ( file_exists(public_path(Storage::url($user_icon->media->media_location_low)))  )
				<img id="user_icon" class="img-responsive" src="{{Storage::url($user_icon->media->media_location_low)}}"/>
			  @endif
		  @endif
	      </div>
	      <div class="name-container">
	        {{$user->name}}
	      </div>
	    </div><!--icon-contianer-->
		<div class="col-md-9 col-sm-9 col-xs-12 summary">
			<div class="row">
				<div class="living-country col-md-4 col-sm-4 col-xs-5">
					<small>主要同遊地區</small>
					<h4>{{ $CountryPresenter->service_country_name($guide->service_country) }}</h4>
				</div>
				<div class="age col-md-3 col-sm-3 col-xs-4 col-sm-offset-2 col-md-offset-2">
					<small>年齡</small>
					<h4>{{$UserPresenter->birth_date_convert_to_age($user->birth_date)}}</h4>
				</div>
				<div class="age col-md-2 col-sm-2 col-xs-3">
					<small>性別</small>
					<p class="h4">{{$user->sex}}</p>
				</div>
			</div>
			<div class="row row-no-padding text-center m-b-22">
			<form id="get_appointment_form">
			<input type="hidden" name="guide_id" value="{{$user->id}}" />
			@if (Auth::guest())
				<button id="get_appointment_form_btn" class="btn submit-btn">
					發送同遊請求
				</button>
			@elseif(Auth::user()->id != $user->id)
				@if(count($gt_match_pending) >= 2)
					<button class="btn submit-btn" disabled>
						已超過請求上限
					</button>
				@else
					<button id="get_appointment_form_btn" class="btn submit-btn">
						發送同遊請求
					</button>
				@endif
			@elseif(Auth::user()->id == $user->id)
				<button class="btn submit-btn">
					這是你自己的介紹頁
				</button>
			@endif
			</form>
			</div>
			<div class="row row-no-padding row-no-margin-bottom">
				<ul class="list-unstyled list-inline text-center row row-no-padding row-no-margin-bottom social_btn_group">
					<li class="col-md-6 col-xs-6 social_btn_middle">
				 		 <button class="remove-btn-default col-md-12 col-sm-12 col-xs-12 followBtn social_btn"></button>
				  </li>
				  <li class="col-md-6 col-xs-6 social_btn_middle">
					  <button class="remove-btn-default col-md-12 col-sm-12 col-xs-12  friendBtn social_btn" data-toggle="dropdown"></button>
				  </li>
				</ul>
			</div>
		</div>
	</div><!--header block-->
	@if(count($match_status) > 0)
	<div class="row m-b-8">
		<div class=" col-lg-12 default_block_style">
			<div class="title row m-b-8">
				<p class="h4">Appointment</p><!--title-->
			</div>
			<div class="table-responsive">
				<table class="table">
					<thead>
					<tr>
						<th>起始日期</th>
						<th>結束日期</th>
						<th>天數</th>
						<th>狀態</th>
					</tr>
					</thead>
					<tbody>
					@foreach($gt_match_accept as $t_match_status)
                        <?php $app_dates = [];?>
						@foreach($t_match_status->appointmentDates as $app_date)
                            <?php
                            array_push($app_dates,$app_date['date']);
                            ?>
						@endforeach
                        <?php $date = $DatePresenter->get_min_and_max_date($app_dates);?>
						<tr>
							<td>{{$date['min_date']}}</td>
							<td>{{$date['max_date']}}</td>
							<td>{{$date['amount']}}</td>
							<td><a href="{{url('GET/scheduleDesk/'.$t_match_status->schedule_id)}}" class="btn btn-success">Open Scuedule</a></td>
						</tr>
					@endforeach
					@foreach($gt_match_pending as $t_match_status)
                        <?php $app_dates = [];?>
						@foreach($t_match_status->appointmentDates as $app_date)
                            <?php
							array_push($app_dates,$app_date['date']);
						 ?>
						@endforeach
						<?php $date = $DatePresenter->get_min_and_max_date($app_dates);?>
						<tr>
							<td>{{$date['min_date']}}</td>
							<td>{{$date['max_date']}}</td>
							<td>{{$date['amount']}}</td>
							<td><a class="cancel-trip-request btn btn-danger" data-appointment_id = '{{$t_match_status->id}}' data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing">取消請求</a></td>
						</tr>
					@endforeach
					@foreach($gt_match_reject as $t_match_status)
                        <?php $app_dates = [];?>
						@foreach($t_match_status->appointmentDates as $app_date)
                            <?php
                            array_push($app_dates,$app_date['date']);
                            ?>
						@endforeach
                        <?php $date = $DatePresenter->get_min_and_max_date($app_dates);?>
						<tr>
							<td>{{$date['min_date']}}</td>
							<td>{{$date['max_date']}}</td>
							<td>{{$date['amount']}}</td>
							<td><span style="color: red">is Rejected</span></td>
						</tr>
					@endforeach
					@foreach($gt_match_expired as $t_match_status)
                        <?php $app_dates = [];?>
						@foreach($t_match_status->appointmentDates as $app_date)
                            <?php
                            array_push($app_dates,$app_date['date']);
                            ?>
						@endforeach
                        <?php $date = $DatePresenter->get_min_and_max_date($app_dates);?>
						<tr>
							<td>{{$date['min_date']}}</td>
							<td>{{$date['max_date']}}</td>
							<td>{{$date['amount']}}</td>
							<td><span style="color: red">Expired</span></td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div><!-- End row appointment-->
	@endif
	@if(count($user_photos) > 0)
	<div class="row m-b-8">
		<div class=" col-lg-12 default_block_style">
			<div class="title row m-b-8">
				<p class="h4">Photos</p><!--title-->
			</div>
			<div class="row">
				@foreach($user_photos as $photo)
				<div class="col-md-4 col-xs-6">
					<div class="media-wrapper m-b-15" style="width:100%;height: 100px;">
						<img class="img-fit-cover photo-style" src="{{$MediaPresenter->img_path($photo['media_path'])}}" style="width: 100; height: 100%">
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
	@endif
	<div class="row m-b-8">
		<div class="col-md-4 col-sm-4 col_md_left">
			<div class="col-md-12 language_block default_block_style">
				<div class="title row m-b-8">
					<p class="h4">Language</p>
				</div>
				@if($user->languages != null)
				<ul class="list-unstyled language_list">
				@foreach($user->languages as $language)
					<li class="row row-no-padding m-b-8">
						<div class="col-md-5 col-xs-4 col-sm-5">
							{{$UserPresenter->language($language->language->language_name)}}
						</div>
						@if($language->level == 2)
						<div class="col-md-6 col-xs-8 col-sm-7">
							<div class="row row_margin_top_5">
								<div class="language_light on"></div>
								<div class="language_light"></div>
							</div>
						</div>
						@elseif($language->level == 3)
						<div class="col-md-6 col-xs-8 col-sm-7">
							<div class="row row_margin_top_5">
								<div class="language_light on"></div>
								<div class="language_light on"></div>
							</div>
						</div>
						@else
						<div class="col-md-6 col-xs-8 col-sm-7">
							<div class="row row_margin_top_5">
								<div class="language_light"></div>
								<div class="language_light"></div>
							</div>
						</div>
						@endif
					</li>
				@endforeach
				</ul>
				@else
				<div>None</div>
				@endif
			</div>
		</div><!--Language block-->
		<div class=" col-md-8 col-sm-8 col-xs-12 col_no_padding">
			<div class="col-md-12  default_block_style service_block">
				<div class="title row m-b-8">
					<p class="h4">Service</p>
					@if(count($user->guide->userServices) > 0)
						<?php
						$user_service_html = [
						    1 => ['img' => 'assistant_001','title' => trans('userInterface.us_assistant')],
                            2 => ['img' => 'photographer_001','title' => trans('userInterface.us_photographer')],
                            3 => ['img' => 'translator_001','title' => trans('userInterface.us_translator')],
						]
						?>
						@foreach($user->guide->userServices as $userService)
							@if(in_array($userService->service_id,[1,2,3]))
							<div class="user_service_thumbnail text-center m-r-15 d-inline-block">
								<img class="icon_thumbnail m-t-10" src="/img/components/us_services/{{$user_service_html[$userService->service_id]['img']}}.png">
								<p class="m-t-10">{{$user_service_html[$userService->service_id]['title']}}</p>
							</div>
							@endif
						@endforeach
					@else
						<h1 class="text-center">None</h1>
					@endif
				</div>
			</div>
		</div><!--service block-->
	</div><!-- End row-->
	@if(count($user->intro_video) > 0)
		<div class="row m-b-8">
			<div class=" col-lg-12 default_block_style">
				<div class="title row m-b-8">
					<p class="h4">Introduction</p><!--title-->
				</div>
				<div class="row m-b-8">
					<div class="col-lg-12 text-center m-b-22">
						<div id="intro_video" style="width:60%;height: 190px" data-video-url="{{$user->intro_video[0]->media->media_location_standard}}"></div>
					</div>
				</div>
			</div>
		</div><!-- End row-->
	@endif
	@if(count($trip_intros) > 0)
	<div class="row">
		<div class=" col-lg-12 trip_Intro default_block_style">
			<div class="title row m-b-8">
				<p class="h4">My Trips</p><!--title-->
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="row  m-b-8">
						<div class="tab-content">
							<?php $i = 0;?>
							@foreach($trip_intros as $trip)
							<?php $fade_in = $i == 0 ? 'in active' : '' ?>
							<div class="tab-pane fade {{$fade_in}}" id="trip_intro_{{$i + 1}}">
								<div class="row trip_content">
									<div class="col-sm-4">
										<div class="trip_media_container">
										<?php $has_video = false?>
										@foreach($trip->trip_media as $trip_media)
											@if($trip_media->media_type == 'video_url')
												<div id="player{{$trip_media->media->media_id}}" class="videoFrame trip_media" data-video-url="{{$trip_media->media->media_location_standard}}">
												</div>
												<?php $has_video = true; ?>
												<?php break ; ?>
											@endif
										@endforeach
										@if($has_video == false)
										@foreach($trip->trip_media as $trip_media)
												@if($trip_media->media_type == 'img')
													<img class="trip_media" style="" src="{{Storage::url($trip_media->media->media_location_standard)}}">
													<?php break ; ?>
												@endif
										@endforeach
										@endif
										</div><!-- trip_media_container -->
									</div>
									<div class="col-sm-8">
										<p class="h4 title" style="margin-top:0">{{$trip->trip_title}}</p>
										<p class="h5 article">{{$trip->trip_description}}</p>
									</div>
								</div>
							</div>
							<?php $i++ ?>
							@endforeach
						</div>
					</div>
					<div class="row">
						<ul class="nav nav-tabs-custom col-lg-12">
							<?php $j=0; ?>
							@foreach($trip_intros as $trip)
							<?php $has_video = false?>
							<li class="trip_nav_list hover-shadow" style="height:120px">
								<a data-toggle="tab" style="width: 100%;height: 80%;" href="#trip_intro_{{$j + 1}}">
								@foreach($trip->trip_media as $trip_media)
									@if($trip_media->media_type == 'video_url')
										<div style="position:absolute;width: 100%;height: 100%;z-index: 10;background: rgba(0,0,0,0);">
										</div>
										<div id="btnPlayer{{$trip_media->media->media_id}}" class="trip_nav_btn url_video" data-video-url="{{$trip_media->media->media_location_standard}}">
										</div>
										<?php $has_video = true; ?>
										<?php break ; ?>
									@endif
								@endforeach
								@if($has_video == false)
									@foreach($trip->trip_media as $trip_media)
										<img class="trip_nav_btn" src="{{Storage::url($trip_media->media->media_location_standard)}}">
										<?php break ; ?>
									@endforeach
								@endif
								</a>
								<p class="text-center omit_text">{{$trip->trip_title}}</p>
							</li>
							<?php $j++ ?>
							@endforeach
						</ul>
					</div><!-- nav-tab -->
				</div>
			</div><!--content-->
		</div><!--trip Introduction block-->
	</div><!-- End row-->
	@endif
</div>
<script>
@if(Auth::guest())
	$('.followBtn').followBtn('unfollow',{{$user->id}});
@elseif($user->id != Auth::user()->id)
	@if($is_Follow)
	$('.followBtn').followBtn('follow',{{$user->id}});
	@else
	$('.followBtn').followBtn('unfollow',{{$user->id}});
	@endif
@endif
@if(Auth::guest())
	$('.friendBtn').friendBtn('notFriend',{{$user->id}});
@elseif($user->id == Auth::user()->id)
@elseif($Friend == null)
	$('.friendBtn').friendBtn('notFriend',{{$user->id}});
@elseif($Friend->status == 0)
	@if($Friend->action_id == Auth::user()->id)
		$('.friendBtn').friendBtn('pending',{{$user->id}});
	@else
		$('.friendBtn').friendBtn('requestGet',{{$user->id}});
	@endif
@elseif($Friend->status == 1)
	$('.friendBtn').friendBtn('isFriend',{{$user->id}});
@elseif($Friend->status == 2)
	@if($Friend->action_id == Auth::user()->id)
		$('.friendBtn').friendBtn('requestRejected',{{$user->id}});
	@else
		$('.friendBtn').friendBtn('pending',{{$user->id}});
	@endif
@endif
$('#cancel_appointment_btn').click(function(e){
  e.preventDefault();
  var form = document.getElementById('get_appointment_form');
  $.ajax({
    url : 'cancel_appointment_for_guide/{{$user->id}}',
    data: new FormData(form),
    processData: false,
    statusCode: {
    	404: function() {
      		alert( "something wrong." );
      	},
      	500: function() {
      		alert( "something wrong." );
      	},
    },
    success : function(data){
    	alert(data.result);
    	window.location = '/';
    },
   })
})
$('#get_appointment_form_btn').click(function(e){
  e.preventDefault();
  if(confirm("在你發送請求同時，也會發交友邀請，你們要成為朋友才能一起同遊。要繼續嗎？") == false) return;
  getAppointmentForm.insertData(new FormData(document.getElementById('get_appointment_form')));
  getAppointmentForm.action();
})
$(document).ready(function(){
    $(".nav-tabs-custom a").click(function(){
        $(this).tab('show');
    });
});
//---
$('.user_modal-close').click(function(){
	$('#userModal').modal('hide');
})
</script>
<script>
    function onPlayerReady(event) {
        event.target.mute();
    }
    $('#intro_video').each(function(){
        $playerId = $(this).attr('id');
        $videoId = getYoutubeId($(this).data('video-url')) ;
        if($videoId == false) return;
        new YT.Player($playerId, {
            playerVars: {controls: 1,autohide:1,wmode:'opaque', iv_load_policy: 3,showinfo : 0},
            videoId: $videoId,
        })

	});
    $('.videoFrame').each(function(){
        $playerId = $(this).attr('id');
        $videoId = getYoutubeId($(this).data('video-url')) ;
        if($videoId == false) return;
        new YT.Player($playerId, {
            playerVars: {autoplay: 1, controls: 0,autohide:1,wmode:'opaque', loop:1, iv_load_policy: 3,showinfo : 0},
            videoId: $videoId,
            loop : 1,
            events: {
                'onReady': onPlayerReady,
                onStateChange: function(e){
					if (e.data === YT.PlayerState.ENDED) {
                        e.target.playVideo();
					}
				}
            }
        });
    })
	$('.trip_nav_btn.url_video').each(function(){
        $playerId = $(this).attr('id');
        $videoId = getYoutubeId($(this).data('video-url')) ;
        if($videoId == false) return;
		new YT.Player($playerId, {
            playerVars: { autoplay: 1, controls: 0,autohide:1,wmode:'opaque' ,loop:1,iv_load_policy: 3,showinfo : 0},
            videoId: $videoId,
            loop : 1,
            events: {
                onReady: onPlayerReady,
                onStateChange: function(e){
                    if (e.data === YT.PlayerState.ENDED) {
                        e.target.playVideo();
                    }
                }
            }
        });
	})
	function getYoutubeId($url){
        $url = String($url);
        var $regex_pattern = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
        if(matches = $url.match($regex_pattern)){
            $id = matches[1];
            return $id;
        }
        return false;
	}
	/*
	*   Bind trip-request function
 	*/
	$('.cancel-trip-request').click(function(e){
        e.preventDefault();
        var $btn = $(this);
        $btn.button('loading');
        cancelTripAppointment($btn.data('appointment_id')).done(function(data){
            alert('已取消請求');
            userProfileModal().reload('{{$user->uni_name}}');
		}).always(function(){
		    $btn.button('reset');
		});

	})
</script>
