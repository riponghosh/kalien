@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Description" content="提供餐廳折價券，居酒屋，酒吧，桌遊，密室逃脫，多買多送。買越多折越多。媒合交友活動平台。">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:url" content="{{Request::url()}}" >
    <meta property="fb:app_id" content="{{env('FB_APP_ID')}}">
    <meta property="og:title" content="@yield('meta_title','歡迎使用Pneko')">
    <meta property="og:image" content="@yield('meta_img', url('img/components/pneko_bw_logo_1.gif'))">
    <meta property="og:height" content="@yield('meta_img_h', 1490)">
    <meta property="og:height" content="@yield('meta_img_w', 1490)">
    <title>Pneko</title>
    <link rel="shortcut icon" href="{{url('img/components/pneko_bw_logo_1.gif')}}" type="image/x-icon"/>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WLVQKGS');</script>
    <!-- End Google Tag Manager -->

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
    <link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/notosanstc.css">

    <!-- Styles -->
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
    {!! Html::style('css/chatRoom/all.css'.VERSION)  !!}
    {!! Html::style('css/app.css'.VERSION) !!}
    {!! Html::style('css/guide/guide.css'.VERSION) !!}
    <!-- JavaScripts -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>

        jQuery.browser={};(function(){jQuery.browser.msie=false; jQuery.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)./)){ jQuery.browser.msie=true;jQuery.browser.version=RegExp.$1;}})();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
    {!! Html::script('js/package/all.js'.VERSION) !!}
    {!! Html::script('js/masonryLayoutPkgd.js'.VERSION) !!}
    {!! Html::script('js/masonryLayout.js'.VERSION) !!}
    {!! Html::script('js/guide.js'.VERSION) !!}
    {!! Html::script('js/schedule/schedule.js'.VERSION) !!}
    {!! Html::script('js/chatRoom/all.js'.VERSION) !!}
    {!! Html::script('js/searchingFilter/all.js'.VERSION) !!}
    {!! Html::script('js/userProfile/all.js'.VERSION) !!}
    {!! Html::script('js/login/login.js'.VERSION) !!}
    {!! Html::script('js/bsModalExtend.js'.VERSION) !!}
    {!! Html::script('js/gobal.js'.VERSION) !!}
    {!! Html::script('js/app.js'.VERSION) !!}
    <style>
        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WLVQKGS"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    @include('layouts.indexNav')
    <!-- Modal -->
    @include('searchingFilter.userFilter_modal')
    <div id="hola" style="display:none">
        <div class="preloader">
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="hola-modal" style="display:none">
        <div class="preloader">
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="modal fade " id="plans_Modal" role="dialog" aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="becomeTouristModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="userModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="ModalForm" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                @include('auth.auth_modal')
            </div>
        </div>
    </div>
    @include('layouts.NotificationBar')
    @yield('header')
    <main style="min-height: 750px;">
    @yield('content')
    </main>
    @include('layouts.footer')
</body>
{!! Html::script('js/socketTest.js'.VERSION) !!}
@yield('script')
@include('layouts.GobalJS')
<script>
    /*
    * FB Init
    */
    window.fbAsyncInit = function() {
        //SDK loaded, initialize it
        FB.init({
            appId      : '{{env('FB_APP_ID')}}',
            xfbml      : true,
            version    : 'v2.2'
        });
    };
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.11';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    $('#userModal').on('hidden.bs.modal',function(){
        $(this).find('.modal-content').children().detach();
    })
    document.getElementById('FBShareBtn').onclick = function() {
        FB.ui({
            method: 'share',
            display: 'popup',
            href: '{{Request::url()}}',
        }, function(response){});
    }
    $(document).ready(function () {
        //Open UserModal by url
        (function(){
            var url = new URL(window.location.href);
            var user_profile_val = url.searchParams.get("user_profile");
            if(user_profile_val == undefined || user_profile_val == '') return;
            openUserProfileModal(user_profile_val);
        })($);
    })
</script>
</html>
