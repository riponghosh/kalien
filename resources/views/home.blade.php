@inject('MediaPresenter','App\Presenters\MediaPresenter')
@extends('layouts.app')
@section('header')
    @if(!BrowserDetect::isMobile()) 
    <header id="hpgy-header">
        <div class="slide_show_container">
            <div class="gallery_wrapper" data-slide="1">
                <div class="container" style="position: absolute;z-index: 1;">
                    <div class="row">
                        <div class="flex gallery_content" style="margin-top: 10%;padding: 0 10% 0 10%">
                            <div class="l-side" style="margin-top:5%;    -webkit-flex: 3;  /* Safari 6.1+ */-ms-flex: 3;  /* IE 10 */    flex: 3;">
                                <p class="title">{{$gallery_activity->trip_activity->title_zh_tw}}</p>
                                <p class="activity-title">{{$gallery_activity->activity_title}}</p>
                            </div>
                            <div class="r-side" style=" padding: 25px; max-width: 300px;  -webkit-flex: 2;  /* Safari 6.1+ */-ms-flex: 2;  /* IE 10 */    flex: 2;">
                                <div class="body" >
                                    <div style="padding-bottom: 15px;">
                                        <div class="userCircle d-inline-block m-r-15" style="width: 65px;height: 65px;">
                                            <?php
                                                $user_icon = optional(optional(optional(optional($gallery_activity->host)->user_icon)[0])->media)->media_location_low;
                                                $user_icon_path = $user_icon == null ?  "img/icon/user_icon_bg.png" : Storage::url($user_icon);
                                            ?>
                                            <img class="img-circle" style="border: 2px #81cfb3 solid;" src="{{$user_icon_path}}"/>
                                        </div>
                                        <div class="name d-inline-block" style="font-size: 24px; vertical-align: middle">
                                            <span style="display: block;font-size:14px;color: #c1c1c1;">主辦人</span>
                                            <span style="display: block;font-size:18px;color: #FFFFFF;">{{$gallery_activity->host->name}}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div>
                                        <ul>
                                            <li class="m-b-10"><span><i class="fa fa-calendar m-r-15"></i></span><span>{{$gallery_activity->start_date}} </span></li>
                                            <li class="m-b-10"><span><i class="fa fa-clock-o m-r-15"></i></span><span>{{date('H:i',strtotime($gallery_activity->start_time))}}</span></li>
                                            <li class="m-b-10"><span><i class="fa fa-map-marker m-r-15"></i></span><span>{{$gallery_activity->trip_activity->map_address_zh_tw}}</span></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="footer text-center">
                                    <a href="{{url('/group_events/'.$gallery_activity->gp_activity_id)}}" class="btn btn-pneko-red btn-lg" target="_blank" style="width: 100%">立刻參加</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="width:100%;background:rgba(0,0,0,0.5);height: 100%;position:absolute;z-index:0;top: 0;left: 0;"></div>
                <img class="gallery_img" src="{{$MediaPresenter->img_path($gallery_activity->trip_activity->trip_activity_cover->media->media_location_standard)}}">
            </div>
            <!--
            <div class="gallery_wrapper" data-slide="1">
                <div class="overlay">
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 table">
                            <div class="place-center">
                            <p class="text-center title">Coming Soon !</p>
                            </div>
                        </div>
                    </div>
                </div>
                <img class="gallery_img" src="{{url('img/background/home_bg_01.jpg')}}">
            </div>
            -->
        </div>
    </header>
    @else
        <header id="hpgy-header">
            <div class="gallery_wrapper" data-slide="1">
                <div class="container">
                    <div class="row">
                        <div class="flex gallery_content" style="margin-top: 10%;padding: 0 10% 0 10%">
                            <div class="place-center" style="-webkit-flex: 1;  /* Safari 6.1+ */-ms-flex: 1;  /* IE 10 */    flex: 1;">
                                <p class="h3 title text-center" style="color: #ffffff; font-weight: 500">{{$gallery_activity->trip_activity->title_zh_tw}}</p>
                                <p class="h2 activity-title text-center" style="color: #ffffff">{{$gallery_activity->activity_title}}</p>
                                <a href="{{url('/group_events/'.$gallery_activity->gp_activity_id)}}" class="btn btn-pneko-red btn" target="_blank" style="width: 100%">立刻參加</a>
                            </div>
                        </div>
                    </div>
                </div>
                <img class="gallery_img" src="{{url('img/background/home_bg_01.jpg')}}">
            </div>
        </header>
    @endif
@endsection
@section('content')
    <div class="container">
        @if(!empty($group_activities))
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>活動推介</h2>
                <p>參加不同類型的活動認識新朋友。</p>
            </div>
        </div>
        <div class="row">
            @include('homePage.groupActivity')
        </div>
        @endif
    </div>
    @if(!Auth::guest())
        <div id="chat_rooms">
        </div>
    @endif
@endsection

@section('script')
    <script>
        /*youtube gallery*/
        function galleryOnPlayerReady(event){
            event.target.mute();
            setTimeout(function() {
                $('#hola').fadeOut();
            }, 700)
        }
        function galleryBeforeLoaded(){
            $('#hola').show();
            setTimeout(function() {
                $('#hola').fadeOut();
            }, 5000)
        }
        $(document).ready(function(){

            $(window).scroll(function(){
                var $gallery = $('#hpgy-header');
                var bottom_of_gallery= $gallery.offset().top + $gallery.outerHeight();
                var bottom_of_window = $(window).scrollTop();
                if(bottom_of_window > bottom_of_gallery){
                    window.setTimeout(showNav, 200);
                }else if(bottom_of_window <= bottom_of_gallery){
                    window.setTimeout(hideNav, 200);
                }
            });
            /*init*/
            $("[data-nav-status='toggle']").addClass("nav-transparent-abs");
            /*hideNav = transpar background; showNav = show by scroll*/
            function hideNav() {
                $("[data-nav-status='toggle']").removeClass("is-visible").addClass("nav-transparent-abs");
            }
            function showNav() {
                $("[data-nav-status='toggle']").removeClass("nav-transparent-abs").addClass("is-visible navbar-fixed-top");
            }
        });
        @if($service_room != null)
            <?php $c_user_icon_path = isset(Auth::user()->user_icons[0]) ? Auth::user()->user_icons[0]->media->media_location_low :null;?>
            Notification.TOKEN = '{{sha1(Auth::user()->socket_token.'add_salt')}}';
        $(function() {
            roomsManager.initialize($('#chat_rooms'), {{Auth::user()->id}});
            roomsManager.create({{$service_room['service_room_id']}}, {
              id: {{Auth::user()->id}},
              profilePic: '{{$MediaPresenter->img_path($c_user_icon_path)}}',
            }, {
              id: {{$service_room['service_officer_id']}},
              name: '{{$service_room['officer_name']}}',
              profilePic: '{{$MediaPresenter->img_path($service_room['officer_icon_path'])}}',
            }, {UI: {CS: true}});
        });
        $(document).ready(function(){
            /*Service Room*/
            $('.service_room_chat_bar').click(function () {
                $(this).parent('.chat_room_container').addClass('expand');
            });
            $('.chat_room.cs_room').find('.close-btn').click(function () {
                $(this).parents('.chat_room_container').removeClass('expand');
            });
            /*
             ** create chatroom
             */
        })
        @endif
    </script>
@endsection
