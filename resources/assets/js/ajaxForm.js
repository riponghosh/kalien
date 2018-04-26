/*
|用在tourist_apply_form_modal
*/
var sendTouristApplyForm = new function(){
  var task = null,
  formData,
  ajax = function(){
    $.ajax({
      url : '/send_tourist_apply_form',
      type : 'POST',
      data: formData,
      contentType: false,
      processData:false,
        statusCode: {
          401: function() {
            login.afterSuccessToDo($.Callbacks().add(sendTouristApplyForm) );
          }
        },
        success : function(data){
          switch(data.status){
            case 'success':
              alert('You are Tourist now.');
              sendTouristApplyForm.executeTask();
              break;
            case 'not_complete':
            $('#becomeTouristModal').modal('show');
              alert('若要預約，請填齊資料。');   
              refreshPage();                
              break;
            case 'error':
              alert(data.msg);
              //window.location = '/';
              break;
            default:
              alert('發生錯誤，請告知客服中心。');
              //window.location = '/';
          }
        }
    });
  },
  refreshPage = function(){
    $('#becomeTouristModal').find('.modal-content').load('/get_tourist_apply_form');
  }
  return{
      action: function(){
        ajax();
      },
      insertData: function(newData){
        formData = newData;
      },
      afterSuccessToDo: function(callback){
        task = callback;
      },
      executeTask: function(){
        if(task == null || task == undefined) return ;
        task.fire();
      }
  }
};
/*
|用在 userProfile modal(guide)
*/
var getAppointmentForm = new function(){
  var formData,
  userId = null,
  ajax = function(){
    $.ajax({
      url : 'GET/appointment_request/',
      type : 'POST',
      data: formData,
      contentType: false,
      cache: false,
      processData:false,
      statusCode: {
        401: function(){
          loginModal.show();
          login.afterSuccessToDo($.Callbacks().add(getAppointmentForm.action) );
        }
      },
      success : function(data){
        switch(data.status){
          case 'true':
            $('#ModalForm').modal('show').find('.modal-content').load(data.url);
            break;
          case 'not_tourist':
            sendTouristApplyForm.afterSuccessToDo($.Callbacks().add(getAppointmentForm.action) );
            $('#becomeTouristModal').modal('show').find('.modal-content').load('/get_tourist_apply_form');
            break;
        }   
      },    
    });
  };
  return{
    action: function(){
      ajax();
    },
    insertData: function(newData){
      formData = newData;
    },
  }
}
