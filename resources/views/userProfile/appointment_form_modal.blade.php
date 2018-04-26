<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
    </button>
    <p class="h4 modal-title">發送預約請求給 <strong style="color: #86d5b9">{{$guide->name}}</strong></p>
</div>
<form id="appointment_form">
{{csrf_field()}}
<div class="modal-body">
  <div class="form-group">
    <h5>預約日期 ：(最多十天)</h5>
    <input type="text" class="form-control input-daterange-timepicker" name="appointment_daterange" autofocus/>
    <input type="hidden" id="appointment_date_start" name="date_start"/><input type="hidden" id="appointment_date_end" name="date_end">
  </div>
  <hr>
  <h5>留言</h5>
  <div class="form-group">
    <textarea class="form-control" rows="5" id="content" name="content"></textarea>
  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="guide_id" value="{{$guide->id}}">
  <input type="hidden" name="room_id" value="{{$chatroom['id']}}">
  <button type="button" id="send_appointment_form_btn" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> sending">submit</button><button type="button" class="btn btn-default" data-dismiss="modal">cancel</button>
</div>
</form>
<script>
/*datepicker*/
var ajaxPost = function(form,btn){
  $.ajax({
    url : 'send_appointment_request_form_to_guide',
    type : 'POST',
    data: new FormData(form),
    contentType: false,
    processData:false,
      statusCode: {
        401: function() {
          window.location = '/login';
        },
        404: function() {
          btn.button('reset');
            alert( "something wrong." );          
          },
          500: function() {
            btn.button('reset');
            alert( "something wrong." );          
          },
      },
      success : function(res){
        btn.button('reset');
        if(res.msg){
            resMsg = res.msg;
        }
        switch(res.success){
          case true:
            alert('success');
            $('#ModalForm').modal('hide');
            break;
          case false:
              if(res.msg){
                  resMsg = res.msg;
              }else{
                  alert('fail')
              }
            break;
          default :
            alert('sorry something wrong !');
            $('#ModalForm').modal('hide');
        }
      }
  })
};
$('#send_appointment_form_btn').click(function(e){
  e.preventDefault();
  /*處理date_start , date_end*/
  var dates = $('input[name="appointment_daterange"]').val().split(' - ');
  $('#appointment_date_start').val(dates[0]);
  $('#appointment_date_end').val(dates[1]);
  var $loadingBtn = $(this);
    $loadingBtn.button('loading');
    setTimeout(function() {
       $loadingBtn.button('reset');
    }, 8000);
  var form = document.getElementById('appointment_form');
  ajaxPost(form,$loadingBtn);
})
$('[data-dismiss = "modal"]').click(function(){
  $(this).closest('.modal').modal('hide');
})
$('.input-daterange-timepicker').daterangepicker({
    format: 'MM/DD/YYYY',
    minDate: 'today',
    buttonClasses: ['btn', 'btn-sm'],
    applyClass: 'btn-default',
    cancelClass: 'btn-primary',
    dateLimit: {
        days: 9
    }
});
</script>
