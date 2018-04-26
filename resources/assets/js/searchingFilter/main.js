function searchingFilter(){
  //----------------------------------
  //  UI
  //----------------------------------
  var searchingFilterUI = new SearchingFilterUI({
    onSend: function(content){
      $.get('/UserSearchingFilter',{content : content},function(response){
        console.log(response);
        $('#showGuide').find($('.main')).html(response);
      });
      searchingFilterUI.closeModal();
      searchingFilterUI.updateToolbar(content);
    },
  });
}
