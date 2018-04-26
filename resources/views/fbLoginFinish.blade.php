<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<body>
</body>
<script language="Javascript" type="text/javascript">
jQuery(document).ready(function(){
    data = {};
    @if($data{'success'})
        @if($data{'data'}{'social_login'} == true)
            data = {success : true};
        @elseif($data{'data'}{'social_login'} == false)
            data = {success : true};
        @endif
    @elseif(!$data{'success'})
        data = {
            success: false,
            msg: '{{$data{'data'}{'msg'} }}'
        };
    @else
        data = {success : false};
    @endif

    opener.SocialLoginCallParent(data);
    window.close();
});
</script>
