<head>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
  {!! Html::style('css/chatRoom/all.css');  !!}
  {!! Html::script('js/chatRoom/all.js'); !!}
  {!! Html::script('js/socketTest.js'); !!}
</head>
<body>
  <div id="chat_rooms"></div>
  {{ csrf_field() }}
  <script>
    <?php $token = Auth::user()->remember_token ;
      $token = sha1($token);
    ?>
    Notification.TOKEN = '{{ $token or null }}';
    $(function(){
      roomsManager.initialize($('#chat_rooms'));
      roomsManager.create(1, { // TODO: room id
        id: {{$current_user->id}},
        profilePic: '/storage/img/userIcon/f7d7fb0dbe4674a8d275dcecd8cb3570433dac71.jpeg',
      }, {
        id: {{$other_user->id}},
        name: '{{$other_user->name}}',
        profilePic: '/storage/img/userIcon/f7d7fb0dbe4674a8d275dcecd8cb3570433dac71.jpeg',
      });
    });
  </script>
</body>
