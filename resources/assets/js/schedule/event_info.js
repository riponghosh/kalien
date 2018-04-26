function EventInfo($content){
  var bindedEvent;
  var sLocked = false, oLocked = false;
  var $topic = $content.find('.topic > input').change(updateData).doneTyping(updateData);
  var $location = $content.find('.location > input').change(updateData).doneTyping(updateData);
  var $description = $content.find('textarea[name="description"]').change(updateData).doneTyping(updateData);

  var $lockContainer = $content.find('.blockLock_container');
  var $lockBtn = $lockContainer.find('.lock_btn').click(function(){
      if (bindedEvent) bindedEvent.updateData({s_locked: (sLocked = !sLocked)});
  });
  var $oLockedMsg = $content.find('.o_locked_msg');
  var $delBtn = $content.find('.delete-eventBlock').click(function(){
    if (!confirm('確認刪除')) return;
    if (bindedEvent) bindedEvent.remove();
    bindWith(undefined);
  });
  bindWith(undefined); //預設沒有綁定行程時，按鈕都要是 disabled
  function eachAttrElement(callback){
    [$topic, $location, $description].forEach(callback);
  }
  var preDesc;
  /*
  var $description = $content.find('textarea[name="description"]');
  bindSummerNoteEditor($description, {
    width: 215,
    height: 300,
    placeholder: '按一下編輯',
    callbacks: {
      onChange: function(contents){
        if (preDesc === contents) return;
        preDesc = contents;
        bindedEvent.updateData({description: contents});
      },
      onImageUpload: function(fileList){
        var data = new FormData();
        data.append('description_image', fileList[0]);
        $.ajax({
          url: '/schedule/eventBlock_description_image',
          cache: false,
          contentType: false,
          processData: false,
          data: data,
          type: "POST",
          success: function(response){
            if (response.status === 'error') return alert('fail');
            $description.summernote('insertImage', '/storage/' + response.img_path);
          },
        });
      }
    },
  });
  */
//---------------------------------------
//  更改面版資料時，同步更新行程
//---------------------------------------
  function updateData($element){
    if (!bindedEvent) return;
    var $element = $(this), hash = {};
    hash[$element.attr('name')] = $element.val();
    bindedEvent.updateData(hash);
  }
//---------------------------------------
//  處理沒有綁定行程時，按鈕的 disabled 情況
//---------------------------------------
  var disabledFlag;
  function setDisabled(flag){
    if (disabledFlag == flag) return;
    disabledFlag = flag;
    eachAttrElement(function($element){ $element.prop('disabled', flag); });
    $delBtn.prop('disabled', flag);
    if (flag){
      $topic.val('請選擇一個行程');
      $location.val('');
    }
  }
//---------------------------------------
//  綁定行程
//---------------------------------------
  function bindWith(event){
    if (event == undefined) setDisabled(true);
    if (bindedEvent) bindedEvent.unbound();
    bindedEvent = event;
    if (event){
      event.bound();
      updateByData(event.getRawData());
      $lockContainer.show();
    }else{
      $lockContainer.hide();
    }
  }
  function updateByData(data){ //data = {from:, to:, topic:, sub_title:, description:}
    if (data.o_locked != undefined) $oLockedMsg.toggle(oLocked = data.o_locked);
    if (data.s_locked != undefined){
      if (sLocked = data.s_locked){
        $lockBtn.removeClass('status-unlock').addClass('status-lock').html('<i class="fa fa-lock" aria-hidden="true"></i>');
      }else{
        $lockBtn.removeClass('status-lock').addClass('status-unlock').html('<i class="fa fa-unlock-alt" aria-hidden="true"></i>');
      }
    }
    /*
    if (data.description !== $description.summernote("code")){
      $description.summernote('code', data.description || ''); // description == undefined 時，要變空字串才會刷新
    }
    */
    setDisabled(sLocked || oLocked);
    eachAttrElement(function($element){ 
      var val = data[$element.attr('name')];
      if (val != undefined) $element.val(val); 
    });
  }
//---------------------------------------
//  ACCESS
//---------------------------------------
  return {
    isDisabled: function(){ return disabledFlag; },
    bindWith: bindWith,
    getBindedEvent: function(){ return bindedEvent; },
    eventChanged: function(event){
      if (bindedEvent != event) return;
      updateByData(event.getRawData());
    },
  };
}
