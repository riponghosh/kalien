/*
|可把一頁暫存，
|除了按backlay部分外，新的modal關閉都會打開暫存的一頁
|
|用法 ： 在需要記錄的地方加入 $('#id').modalSteps;
*/
jQuery.fn.modalSteps = function(url){
    if(url != undefined)modalStep.reloadUrl = url;

  $.when($('[data-page=true]').removeAttr('data-page')).done($(this).attr('data-page','true'));  
  /*
  **按backDrop 部分會刪除所 lastPage資料
  */
  $('.modal').click(function(e){
    if(e.target !== this)return;
    $('[data-page=true]').removeAttr('data-page');
  })
}
 var modalStep = {
    page: null,
    reloadUrl: null,
    showRecordPage: function(){
      if(this.page != null)return;
      if(this.reloadUrl != null) {
          $('[data-page=true]').modal('show').find('.modal-content').load(this.reloadUrl);
      }else{
          $('[data-page=true]').modal('show');
      }
    }
}

