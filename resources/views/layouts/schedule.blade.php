<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WLVQKGS');</script>
    <!-- End Google Tag Manager -->

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" crossorigin="anonymous">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
    {!! Html::script('js/masonryLayoutPkgd.js'.VERSION) !!}
    {!! Html::script('js/masonryLayout.js'.VERSION) !!}
    {!! Html::script('js/chosen.jquery.min.js'.VERSION) !!}
    {!! Html::script('js/guide.js'.VERSION) !!}
    {!! Html::script('js/jquery.cookie.js'.VERSION) !!}
    {!! Html::script('js/package/all.js'.VERSION) !!}
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
<body id="app-layout" data-spy="scroll" data-target=".sidebar">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WLVQKGS"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@include('layouts.indexNav')
<!-- Modal -->
@include('searchingFilter.userFilter_modal')
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
<main>
    @yield('content')
</main>
</body>
{!! Html::script('js/socketTest.js'.VERSION) !!}
</html>
