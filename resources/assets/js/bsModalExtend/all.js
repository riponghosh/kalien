/*
**打開新Modal前自動關閉舊Modal
*/
var closeOldModalBeforeOpenNewModal = function(newModal,e){
  var hideAll = function(){
    $('.modal').modal('hide');
  }
  var showNewModal = function(){
    newModal.modal('show'); 
  }
  //防止同一個modal重複打開
  if($('.modal.fade.in').attr('id') == newModal.attr('id')){
    e.preventDefault();
    return;
  }
  if($('.modal.fade.in').length > 0){
   e.preventDefault();
   $.when(hideAll()).done(showNewModal());
  }
  
}


