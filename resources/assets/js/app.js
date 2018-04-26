//require('./bootstrap');

$(document).ready(function(){
  /*****************************************
  **Bootstrap 初始化 加入返回記錄頁方法
  ******************************************/
  $('.modal').on('show.bs.modal',function(e){
      //$('.hola-modal').fadeIn();
    /*暫存頁，待所有事件完成後才打開*/
    modalStep.page = $(this);
    /*已有打開modal的情況下，會觸發此事件：關閉所有modal，再打開新的。*/
    closeOldModalBeforeOpenNewModal($(this),e);   
  })
  $('.modal').on('shown.bs.modal',function(){
    /*modal 打開後，清除Tmp內容，事件完結*/
    //$('.hola-modal').fadeOut();
    modalStep.page = null;
    //-----------------------------------------
    // 分頁url 處理
    // -----------------------------------------
    if($(this).attr('id') == 'userModal'){
      window.history.replaceState('', '', addURLParameter(window.location.href, 'user_profile', $(this).attr('data-user-name')));
    }
  })
  $('.modal').on('hidden.bs.modal',function(){
    /*hide不是記錄頁*/
    if(!$(this).attr('data-page')){
      /*且沒有暫存頁*/
      modalStep.showRecordPage();
    }
    //-----------------------------------------
    // 分頁url 處理
    // -----------------------------------------
    window.history.replaceState('', '', removeURLParameter(window.location.href, 'user_profile'))
  });
  /*****************************************
  ** ajax設定
  ******************************************/ 
  $.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  timeout: 5000,
  type: 'POST',
  cache: false,
  statusCode: {
    401: function() {
      //window.open('/login');
    }, 
    404: function() {
      alert('Something wrong');
    }
  },
  beforeSend : function (xhr,btn){
  },
  complete:function () {
  },
  });
    $('.open-my-plans-modal').on('click',function(e){
        var url = $(this).attr('href');
        e.preventDefault();
        $.ajax({
            url : $(this).attr('href'),
            type: 'GET',
            statusCode: {
                500: function(){
                    alert('Something wrong.');
                }
            },
            success : function(data){
                $('#ModalForm').modal('show').find('.modal-content').html(data);
            }
        })
    })
  $('.open-dashboard-modal').on('click',function(e){
  var url = $(this).attr('href');
  e.preventDefault();
  $.ajax({
  url : $(this).attr('href'),
  type: 'GET',
  statusCode: { 
    500: function(){
        alert('Something wrong.');
    }
  },
  success : function(data){
      $('#ModalForm').modal('show').find('.modal-content').html(data);
  }
  })  
  })
  /*
  **Tooptip
  */
  $('[data-toggle="tooltip"]').tooltip();
  /*
  ** Rating
  */
    $.fn.extend({
        rating : function(rates){
            for(var i = 1; i <= 5; ++i){
                var className = (i <= rates ? 'star-active' : 'star-inactive');
                var $star = $('<i class="fa fa-star" aria-hidden="true">').addClass(className);
                $(this).append($star);
            }
        }
    })
    // right side-bar toggle
    $('.right-bar-toggle').on('click', function(e){
        $('#notification_bar').toggleClass('right-bar-enabled');
    });
    /*
    **Img On Error problem
    */
    $.fn.extend({
        img_src_exist : function(){
            if($(this).attr('src') == null || $(this).attr('src') == ''){
                $(this).attr( "src", "/img/icon/user_icon_bg.png" );
                return;
            }
            $(this).on('error',function(){
                $(this).attr( "src", "/img/icon/user_icon_bg.png" );
                return;
            })

        }
    })
    $(document).ready(function(){
        $('.user-icon').each(function(){
            $(this).img_src_exist();
        })
    })


});



