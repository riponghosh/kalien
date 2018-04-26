@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('auth.Login')</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder="@lang('auth.EmailAddress')">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control" name="password" required placeholder="@lang('auth.password')">
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> @lang('auth.RememberMe')
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-5 pull-right">
                                <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                    @lang('auth.ForgotYourPassword')?
                                </a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary" style="width:100%">@lang('auth.Login')</button>
                            </div>
                        </div>
                    </form>
                    <div class="row m-b-10">
                        <div class="col-md-12 text-center">
                            或者
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <a class="fb-login btn btn-default" style="width:100%;background: #3b5998;color: #ffffff"><i class="fa fa-facebook m-r-15 fa-lg" aria-hidden="true"></i>Continue Facebook</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$('.fb-login').click(function(){
    fbLogin.afterSuccessToDo($.Callbacks().add(refreshAfter));
    window.open('{{url('/auth/facebook')}}','test','width=780,height=500,directories=no,location=no,menubar=no');
})
</script>
@endsection