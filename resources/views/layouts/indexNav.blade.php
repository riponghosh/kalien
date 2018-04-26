<?php
use App\Http\Controllers\UserProfileController;
$is_tourGuide =  UserProfileController::auth_user_is_tourGuide();
$is_tourist =  UserProfileController::auth_user_is_tourist();
?>
<nav class="navbar-style navbar-default" id="app-nav" data-nav-status="toggle">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                Pneko
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav  navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">@lang('app.Login')</a></li>
                    <li><a href="{{ url('/register') }}">@lang('app.Register')</a></li>
                @else
                    @if ($is_tourGuide == null)
                        <li>
                            <a href="{{url('/guide_application')}}" class="get-guide-apply-form" target="_blank">
                                <i class="fa fa-address-card-o" aria-hidden="true"></i>
                                @lang('app.BecomePneker')
                            </a>
                        </li>
                    @endif
                    @if($is_tourGuide != null)
                        <li>
                            <a href="{{url('/GET/tourist_request_dashboard_modal')}}" class="guide-request-dashboard open-dashboard-modal">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                Travel request
                            </a>
                        </li>
                    @endif
                    <li id="notification-btn" class="notification-btn">
                        <a class="right-bar-toggle">
                            <i class="fa fa-bell fa-lg" aria-hidden="true"></i>
                        </a>
                        <div class="noti-dot">
                            <span class="dot"></span>
                            <span class="pulse"></span>
                        </div>
                    </li>
                    <li id="open-my-plans-modal-btn">
                        <a href="{{url('/GET/plans_modal')}}" class="open-my-plans-modal">
                            <i class="fa fa-calendar fa-lg" aria-hidden="true"></i>
                        </a>
                    </li>
                        <li id="open-my-tickets-btn">
                            <a href="{{url('/my_ticket')}}">
                                <i class="fa fa-ticket fa-lg" aria-hidden="true"></i>
                            </a>
                        </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="{{url('/user/abouts')}}">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    @lang('app.AccountSetting')
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    <i class="fa fa-btn fa-sign-out"></i>
                                    @lang('app.Logout')
                                </a>

                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>