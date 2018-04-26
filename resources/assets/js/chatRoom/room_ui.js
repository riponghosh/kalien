//-----------------------------------------------------------------
// ● 聊天室 UI (room.js 內部使用的物件)
//
// == Params
//
// * [$container] 聊天室的 parent 元素
// * [currentUser] 自己的一些資訊（格式參照 room.js 內說明）
// * [otherUser] 對方的一些資訊（格式參照 room.js 內說明）
// * [callbacks] 回調函式
// * * .onSend: 發送訊息時
//     [content] 訊息內容
//
// * * .loadOldMsg: 要載入舊訊息時
//     [oldestKey] 最舊的訊息 id
//     [onSuccess] 載入完後的回調函式
//
//-----------------------------------------------------------------
function RoomUI($container, roomID, currentUser, otherUser, options, callbacks){
  if (options == null) options = {};
  if (callbacks == null) callbacks = {};
//---------------------------------
//  content
//---------------------------------
  var $content, $titleContainer;
  if (options.CS){ 
    $content = $(
      '<div class="chat_room_container cs_room_container" data-room_id="">' +
        '<div class="service_room_chat_bar">' +
          '<div class="omit_text title">' +
            '<span>Customer Service Officer</span><i class="fa fa-comments p-l-20" aria-hidden="true"></i>' +
          '</div>' +
        '</div>' +
        '<div class="chat_room cs_room">' +
          '<div class="title text-center">' +
            '<i class="fa fa-times close-btn" aria-hidden="true"></i>' +
            '<div class="head-container">' +
              '<div class="name-container">' +
                '<div class="omit_text name"><span>' + otherUser.name + '</span></div>' +
                '<div class="omit_text sub_name"><span>客服專員</span></div>' +
              '</div>' +
            '</div>' +
            '<hr>' +
            '<span class="description">你的疑問會在1天內回覆，謝謝。</span>' +
          '</div>' +
        '<div class="msgs_container"></div>' +
          '<div class="send_msg_area">' +
            '<input type="text" placeholder="寫下信息﹍">' +
          '</div>' +
        '</div>' +
      '</div>'
    );
    $titleContainer = $content.find('.head-container');
  }else{
    $content = $(
      '<div class="chat_room_container normal_room_container expand normal_size" data-room_id="">' +
        '<div class="chat_room normal_room">' +
          '<div class="title">' +
            '<div class="omit_text name"><span>' + otherUser.name + '</span></div>' +
            '<i class="fa fa-times remove_room_btn pull-right" aria-hidden="true"></i>' +
          '</div>' +
          '<div class="msgs_container"></div>' +
          '<div class="send_msg_area">' +
            '<input type="text" placeholder="寫下信息﹍">' +
          '</div>' +
        '</div>' +
      '</div>'
    );
    $titleContainer = $content.find('.title');
  }
  $content.attr('data-room_id',roomID);
  var $msgContainer = $content.find('.msgs_container');
  $titleContainer.prepend($('<img class="icon">').attr('src', otherUser.profilePic));
//--------------------------------
// 強制把新chatroom加在cs_room左邊
//--------------------------------
  $cs_room_exist = $container.find('.cs_room_container');
  if($cs_room_exist.length > 0){
      $content.insertBefore($cs_room_exist);
  }else {
      $container.append($content);
  }
//--------------------------------
// 聊天室expand
//--------------------------------
  $titleContainer.click(function(){
    $content.toggleClass('expand');
  })
//---------------------------------
//  (UI) 由msg資料創$msg html
//---------------------------------
  function createMsgHtmlBy(msg){ //msg = {content:, sent_by:}
    var $msg = $('<div class="msg_wrapper">');
    if (msg.sent_by == currentUser.id){
      $msg.append($('<div class="msg self">').text(msg.content));
    }else{
      $msg.append($('<div class="msg other">').text(msg.content));
    }
    return (msg.$msg = $msg);
  }
//---------------------------------
//  管理訊息順序
//---------------------------------
  var allMsgsOrderManager = new function(){
    var mapping = {}, minKey = -1, maxKey = -1, needScrollToBottom = false;
    function scrollToBottomIfNeeded(){
      if (needScrollToBottom == false) return;
      needScrollToBottom = false;
      $msgContainer.scrollTop($msgContainer[0].scrollHeight);
    }
    function appendBottom($msg){
      if ($msgContainer.scrollTop() + $msgContainer[0].clientHeight == $msgContainer[0].scrollHeight){ //at bottom
        needScrollToBottom = true;
      } 
      $msgContainer.append($msg);
    }
  //---------------------------------
  //  (UI) 新增訊息
  //---------------------------------
    function addOne(msg, $msg){ //msg = {content:, created_at:, key:, sent_by:, $msg}
      if (mapping[msg.key] != undefined) return false;
      mapping[msg.key] = msg;
      if ($msg == undefined) $msg = createMsgHtmlBy(msg);
      if (minKey == -1){ //key是順序，由key去搜尋這段訊息要插入在哪裡
        minKey = msg.key;
        maxKey = msg.key;
        appendBottom($msg);
      }else if (msg.key < minKey){
        minKey = msg.key;
        $msgContainer.prepend($msg);
      }else if (msg.key > maxKey){
        maxKey = msg.key;
        appendBottom($msg);
      }else{
        for(var key = msg.key - 1; key >= minKey; key--){
          var otherMsgObj = mapping[key];
          if (otherMsgObj == undefined) continue;
          otherMsgObj.$msg.after($msg);
          break;
        }
      }
    }
  //---------------------------------
  //  (UI) 新增未同步訊息
  //---------------------------------
    function tmpAdd(msg){ //前臺已傳送訊息先顯示在最下，等待後臺回覆key值再重算位置
      if (msg.key != undefined) return; 
      var $msg = createMsgHtmlBy(msg);
      $msg.css('opacity', 0.5); //TODO 訊息還沒有確定傳送成功時要顯示怎樣？
      appendBottom($msg);
      return function(key, created_at){ //與後臺溝通完後的callback
        if (key == undefined){ //失敗
          $msg.remove(); //TODO 訊息傳送失敗時要做什麼？
        }else{ //成功
          msg.key = key;
          msg.created_at = created_at;
          $msg.css('opacity', 1); //TODO 訊息還沒有確定傳送成功時要顯示怎樣？
          addOne(msg, $msg);
        }
      };
    }
  //---------------------------------
  //  ACCESS
  //---------------------------------
    return {
      getMinKey: function(){ return minKey; },
      add: function(msgs){
        msgs.forEach(function(msg){ addOne(msg); })
        scrollToBottomIfNeeded();
      },
      tmpAdd: function(msg){
        var onConfirm = tmpAdd(msg);
        scrollToBottomIfNeeded();
        return onConfirm;
      }
    };
  };
//---------------------------------
//  (Event) 訊息框事件
//---------------------------------
  var $msgInput = $content.find('.send_msg_area > input').keyup(function(event){
    if (event.keyCode != 13) return;
    var content = $msgInput.val();
    if (content == '') return;
    $msgInput.val('');
    callbacks.onSend(content);
  });
//---------------------------------
//  (Event) 捲動自動載入舊訊息
//---------------------------------
  ;(function(){
    var top = 0, doFlag = false;
    $msgContainer.scroll(function(){
      var newTop = $msgContainer.scrollTop();
      if (doFlag == false && newTop < 200 && newTop - top < 0){ //往上捲且只剩不到200px可以捲
        doFlag = true;
        callbacks.loadOldMsg(allMsgsOrderManager.getMinKey(), function(){ doFlag = false; });
      }
      top = newTop;
    });  
  })();
//---------------------------------
//  (Event) 刪除聊天室
//---------------------------------
  var $roomCloseBtn = $content.find('.remove_room_btn').click(function(){
      callbacks.removeRoomSelf();
  });
  return {
  //---------------------------------
  //  新增訊息
  //---------------------------------
    createMsgs: function(msgs, prependFlag){ //msg = {content:, created_at:, key:, sent_by:, $msg}
      if (prependFlag){
        var oriHeight = $msgContainer[0].scrollHeight;
        var oriScroll = $msgContainer.scrollTop();
        allMsgsOrderManager.add(msgs);
        $msgContainer.scrollTop(oriScroll + $msgContainer[0].scrollHeight - oriHeight); //restore "scroll position"
      }else{
        allMsgsOrderManager.add(msgs);  
      }
    },
  //---------------------------------
  //  新增未同步訊息
  //---------------------------------
    pseudoCreate: function(msg){ //還未與後臺溝通，只是前臺先顯示出來
      return allMsgsOrderManager.tmpAdd(msg);
    },
    remove: function(){
      $content.remove();
    }
  };
}
