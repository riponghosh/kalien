<ul class="nav nav-tabs nav-justified">
    <li class="active"><a href="#login_page" data-toggle="tab">Login</a></li>
    <li><a href="#register_page" data-toggle="tab">Register</a></li>
</ul>
<div class="tab-content">
    <div id="login_page" class="tab-pane active">
        <div class="modal-body">
            @include('auth.login_modal')
        </div>
    </div>
    <div id="register_page" class="tab-pane">
        <div class="modal-body">
            @include('auth.register_modal')
        </div>
    </div>
</div>
<script>
    $('#loginModal').find('.close').click(function(){
        $('#loginModal').modal('hide');
    })
    $('.fb-login-m').click(function(){
        fbLogin.afterSuccessToDo($.Callbacks().add(login.executeTask));
        window.open('{{url('/auth/facebook')}}','test','width=780,height=500,directories=no,location=no,menubar=no');
    })
</script>
