<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <a class="fb-login-m btn btn-default" style="width:100%;background: #3b5998;color: #ffffff"><i class="fa fa-facebook m-r-15 fa-lg" aria-hidden="true"></i>Continue Facebook</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 text-center m-b-15">
        或者
    </div>
</div>
<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <form class="form-horizontal" role="form" id="registerForm">
            <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                <div class="col-xs-6">
                    <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('name') }}" required autofocus placeholder="first name">
                    @if ($errors->has('first_name'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="col-xs-6">
                    <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('name') }}" required placeholder="last name">
                    @if ($errors->has('first_name'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <div class="col-xs-12">
                    <input id="register-email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required>
                    @if ($errors->has('email'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <div class="col-xs-12">
                    <input id="register-password" type="password" class="form-control" name="password" placeholder="password" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                </div>
            </div>
            <div class="form-group{{ $errors->has('sex') ? ' has-error' : '' }}">
                <div class="col-xs-5">
                    <select name="sex" class="form-control" required="required">
                        <option selected="true" disabled="disabled">Gender</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                </div>
                <div class="col-xs-12">
                    @if ($errors->has('sex'))
                        <span class="help-block">
                                        <strong>Required !</strong>
                                </span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <button type="button" class="btn btn-primary" style="width:100%;" id="registerSubmitBtn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> 註冊中">
                        Register
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#registerSubmitBtn').click(function(){
        $btn = $(this);
        $btn.button('loading');
        submitRegisterForm().done(function(){
            authCheck().done(function(response){
                if(response.status == 'ok'){
                    login.executeTask();
                }else if(response.status == 'error'){
                    alert('登入失敗，請再次登入');
                }
            });;
        }).fail(function(){
            alert('請檢查資料');
        }).always(function(){
            $btn.button('reset')
        });
    })
    function submitRegisterForm(){
        return $.ajax({
            url: '{{ url('/register') }}',
            type: 'POST',
            data: $('#registerForm').serialize()
        })
    }
</script>
