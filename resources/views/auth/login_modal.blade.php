<div class="row">
    <div class="col-md-10 col-md-offset-1">
                <form class="form-horizontal" role="form" id="loginForm">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <div class="col-md-12">
                            <input id="email" type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <div class="col-md-12">
                            <input id="password" type="password" class="form-control" name="password" placeholder="password" required>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember"> Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 pull-right">
                            <a class="btn btn-link" href="{{ url('/password/reset') }}" target="_blank">
                                <i class="fa fa-lock"></i> Forgot Password?
                            </a>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" style="width:100%;" id="loginSubmitBtn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> 登入中">
                                Login
                            </button>
                        </div>
                    </div>
                </form>
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-center m-b-15">
        或者
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <a class="fb-login-m btn btn-default" style="width:100%;background: #3b5998;color: #ffffff"><i class="fa fa-facebook m-r-15 fa-lg" aria-hidden="true"></i>Continue Facebook</a>
    </div>
</div>
<script>
$('#loginSubmitBtn').click(function(){
    $btn = $(this);
    $btn.button('loading');
    submitLoginForm().done(function(){
        authCheck().done(function(response){
            if(response.status == 'ok'){
                login.executeTask();
            }else if(response.status == 'error'){
                alert('登入失敗，請再次登入');
            }
        });
    }).fail(function(){
        alert('請檢查資料');
    }).always(function(){
        $btn.button('reset')
    });
})
function submitLoginForm(){
    return $.ajax({
        url: '{{ url('/login') }}',
        type: 'POST',
        data: $('#loginForm').serialize()
    });
}
function authCheck(){
    return $.ajax({
        url: '{{url('/authCheck')}}',
        type: 'POST',
    });
}
</script>
