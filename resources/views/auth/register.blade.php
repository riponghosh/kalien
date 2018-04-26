@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('auth.Register')</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        {{ csrf_field() }}

                        <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="first_name" class="col-xs-12 col-md-4 control-label-sm-left control-label">@lang('auth.Name')</label>

                            <div class="col-xs-6 col-sm-6 col-md-3 col-xs-6">
                                <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('name') }}" required autofocus placeholder="@lang('auth.FirstName')">
                                @if ($errors->has('first_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-3 col-xs-6">
                                <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('name') }}" required placeholder="@lang('auth.LastName')">
                                @if ($errors->has('first_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif                              
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">@lang('auth.EmailAddress')</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">@lang('auth.password')</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">@lang('auth.ConfirmPassword')</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('sex') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label" for="gender">@lang('auth.Gender')</label>
                            <div class="col-md-6">
                            <label for="male" class="col-md-4 control-label radio-inline">
                                <input type="radio" id="male" name="sex" value="M" required="required"
                                {{ old('sex')=="M" ? 'checked='.'"'.'checked'.'"' : '' }}/>@lang('auth.Male')
                            </label>
                            <label class="col-md-4 control-label radio-inline">
                                <input type="radio" name="sex" value="F" 
                                {{ old('sex')=="F" ? 'checked='.'"'.'checked'.'"' : '' }}>@lang('auth.Female')
                            </label>
                            @if ($errors->has('sex'))
                                <span class="help-block">
                                        <strong>Required !</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('auth.Register')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
