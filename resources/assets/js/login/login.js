/*
---------------------
|建立login Modal 
|例用法 ： $('body').loginModal(); 
---------------------
*/
var loginModal = new function(){
    return{
        show: function(){
            if($('#loginModal').length == 0)return;
            $('#loginModal').modal('show');
            return;
        }
    }
}
/******************************************************
**登入成功處理
**pass function至登入成功function
******************************************************/
var fbLogin = new function(){
    var task;
    var taskValue;
    return{
        afterSuccessToDo: function(callback,value){
            task = callback;
            taskValue = value;
        },
        executeTask: function(){
            if(task == null) return;
            task.fire(taskValue);

        }
    }
}
var login = new function(){
  var task;
  var taskValue;
  return{
      afterSuccessToDo: function(callback,value){
        task = callback;
        taskValue = value;
      },
      executeTask: function(){
          if(task == undefined) return;

          if(typeof taskValue === 'object' ){
              $.when( $('#loginModal').modal('hide')).done(task.fireWith(window,taskValue));
          }else{
              $.when( $('#loginModal').modal('hide')).done(task.fire(taskValue));
          }



      }
  }
}
/******************************************************
 **FB登入
 **
 ******************************************************/

SocialLoginCallParent = function (data) {
    if(data.success == true){
        authCheck();
        fbLogin.executeTask();
    }else if(!data.success){
        errMsg = (data.msg == undefined || data.msg == '') ? '登入失敗' : data.msg;
        toastr['error'](errMsg);
        toastr.options = {
            "timeOut": "15000",
        }
    }
}
function refreshAfter(){
        window.location.reload();
}
