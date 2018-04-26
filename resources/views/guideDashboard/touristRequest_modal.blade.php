@inject('DatePresenter','App\Presenters\DatePresenter')
@if(count($request_tourist) > 0)
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
    </button>
    <p class="h4 modal-title">遊客請求</p>
</div>
<div class="modal-body">
  <div class="table-responsive">
  <table class="table table-hover table-striped">
    <thead>
      <tr>
        <th>User</th>
        <th>Start-Date</th>
        <th>End-Date</th>
        <th>Comment</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($request_tourist as $tourist)
      @if(isset($tourist->userTourist))
          <?php $app_dates = [];?>
          @foreach($tourist->appointmentDates as $app_date)
              <?php
              array_push($app_dates,$app_date['date']);
              ?>
          @endforeach
          <?php $date = $DatePresenter->get_min_and_max_date($app_dates);?>
      <tr id="tourist_row_{{$tourist->tourist_id}}">
        <td>
          {{$tourist->userTourist->name}}
        </td>
        <td>
          {{$date['min_date']}}
        </td>
        <td>
          {{$date['max_date']}}
        </td>
        <td>
        @if($tourist->msg && $tourist->msg_sentby != Auth::user()->id)
          <a class="open-chatroom-btn" data-toggle="tooltip" title="Reply !" data-roomId="{{$tourist->room_id}}">{{$tourist->msg}} <i class='fa fa-comment-o aria-hidden="true'></i></a>
        @else
                <a class="open-chatroom-btn" data-toggle="tooltip" title="Reply !" data-roomId="{{$tourist->room_id}}">no new comment.</a>
        @endif
        </td>
        <td>
          @if($tourist->status ==1)
          <a href="{{url('GET/scheduleDesk/'.$tourist->schedule_id)}}" class="btn btn-success">Open Scuedule</a>
          @elseif($tourist->status ==2)
          <a href="{{url('/accept_appointment_by_guide')}}" class="accept-request btn btn-success" data-appointment_id="{{$tourist->id}}" data-touristid="{{$tourist->tourist_id}}" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing">accept</a>
          <a href="{{url('/reject_appointment_by_guide/'.$tourist->id)}}" class="reject-request btn btn-danger" data-appointment_id="{{$tourist->id}}" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing">reject</a>
            @endif
        </td>
      </tr>
      @endif
      @endforeach
    </tbody>
  </table>
  </div>
</div><!--/modal body-->
@else
<div style="text-align: center; padding: 20px;">
  No any request.
</div>
@endif
<script>
/*
**  Open chatroom
*/
$('.open-chatroom-btn').click(function(){
    open_chat_room($(this).attr('data-roomId'));
})

var rejectAjax = function(btn){
  $.ajax({
    url : btn.attr('href'),
    type : 'GET',
    contentType: false,
    cache: false,
    processData:false,
      statusCode: {
        404: function() {
          btn.button('reset');
            alert( "something wrong." );          
          },
          500: function() {
            btn.button('reset');
            alert( "something wrong." );          
          },
      },
      success : function(data){       
        btn.button('reset');
        alert('rejected the request succeed!')
        $('#ModalForm').find('.modal-content').load('{{url('/GET/tourist_request_dashboard_modal')}}');
      }
    });//ajax

}
/**
**reject btn
**/
$('.reject-request.btn').click(function(e){
  e.preventDefault();
  var $btn = $(this);
  $btn.button('loading');
  rejectTripAppointment($btn.data('appointment_id')).done(function(res){
      if(res.success == true) {
          alert('reject the request succeed!');
      }else if(res.success == false){
          alert('失敗，正在重新為你導入頁面');
      }else{
          alert('失敗，正在重新為你導入頁面');
      }
  }).always(function(){
      $btn.button('reset');
      $('#ModalForm').find('.modal-content').load('/GET/tourist_request_dashboard_modal');
  })

})
/**
**accept btn
**/
$('.accept-request.btn').click(function(e){
    e.preventDefault();
    var $btn = $(this);
    acceptTripAppointment($btn.data('appointment_id')).done(function(res){
        if(res.success == true) {
            alert('accept the request succeed!');
            window.location = '/';
        }else if(res.success == false){
            alert('失敗，正在重新為你導入頁面');
        }else{
            alert('失敗，正在重新為你導入頁面');
        }
    }).always(function(){
        $btn.button('reset');
        $('#ModalForm').find('.modal-content').load('/GET/tourist_request_dashboard_modal');
    })
})
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
