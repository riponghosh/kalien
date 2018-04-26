@inject('MediaPresenter','App\Presenters\MediaPresenter')
@extends('layouts.app')
@section('header')
    <header id="hpgy-header">
        <div class="slide_show_container">
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
        </div>
    </header>
@endsection
@section('content')
    <!--
    <div class="container p-b-75 dp-button-group">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1>目的地推薦</h1>
                <p>選擇你的旅遊地點並尋找當地人做你的伙伴。</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-6 dp-button-container">
                <a>
                    <img src="img/components/dp_bg_tpi.jpg" class="img-rounded">
                    <p class="text-center title">台北</p>
                </a>
            </div>
            <div class="col-md-4 col-sm-6 dp-button-container">
                <a>
                    <img src="img/components/dp_bg_mo.png" class="img-rounded">
                    <p class="text-center title">澳門</p>
                </a>
            </div>
        </div>
    </div><!-- Destination Place Container-->
    <div class="container">
        <div class="row">
            @include('homePage.groupActivity')
        </div>
        <!--
        <div class="row">
            <div class="col-md-12 text-center">
            <h1>尋找在地旅遊伙伴</h1>
            <p>由當地人與你一起安排旅遊行程，讓你發掘未知好玩的地方和地道價廉物美的美食，更加了解當地人的生活。</p>
            </div>
        </div>
        <div class="row">
            {{--@include('homePage.showTourGuide')--}}
        </div>
        -->
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
