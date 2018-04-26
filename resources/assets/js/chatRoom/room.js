//-----------------------------------------------------------------
// ● 生成一個聊天室
//
// == Params
//
// * [$container] 聊天室的 parent 元素
// * [roomID] 後臺的 room id，API會需要用到的參數
// * [currentUser] 自己的一些資訊
//    格式：{
//      id: [ID],
//      profilePic: [大頭圖的圖片路徑],
//    }
//
// * [otherUser] 對方的一些資訊
//    格式：{
//      id: [ID],
//      name: [名字],
//      profilePic: [大頭圖的圖片路徑],
//    }
//
//-----------------------------------------------------------------
function createChatRoom($container, roomID, currentUser, otherUser, options, callbacks){
  if (options == null) options = {};
  if (callbacks == null) callbacks = {};
  var postToken = $('input[name="_token"]').val();
  $.get('/chatRoom/getMsg/' + roomID, function(response){
    if(response.success == false) return;
    sync(response.data, false);
  });
//---------------------------------
//  UI
//---------------------------------
  var roomUI = new RoomUI($container, roomID, currentUser, otherUser, options.UI, {
  //---------------------------------
  //  發送訊息
  //---------------------------------
    onSend: function(content){
      var onConfirm = roomUI.pseudoCreate({sent_by: currentUser.id, content: content});
      $.post('/chatRoom/sendMsg', {room_id: roomID, content: content, _token: postToken}, function(response){
        if (response == undefined) onConfirm();
        else onConfirm(response.key, response.created_at);
      });
    },
  //---------------------------------
  //  載入舊訊息
  //---------------------------------
    loadOldMsg: function(oldestKey, onSuccess){
      if (oldestKey <= 1) onSuccess(); //第一個訊息key是1
      else $.get('/chatRoom/getMoreMsg/' + roomID + '/' + oldestKey, function(response){
        if(response.success == true){
            sync(response.data, true); onSuccess();
        }
      });
    },
  //---------------------------------
  //  在room managers 刪除聊天室自身
  //---------------------------------
    removeRoomSelf: function(){
      callbacks.destroyRoom();
    }
  });
//---------------------------------
//  同步訊息
//---------------------------------
  function sync(msgs, prependFlag){ 
    msgs.forEach(function(msg){ //msg = {content:, created_at:, key:, sent_by:, $msg}
      msg.key = parseInt(msg.key); //API傳的是字串
      msg.sent_by = parseInt(msg.sent_by); //API傳的是字串
    });
    roomUI.createMsgs(msgs, prependFlag);
  }
//---------------------------------
//  刪除聊天室
//---------------------------------
  function destroySelf(){
      roomUI.remove();
  }
  return {
    destroySelf: destroySelf,
    sync: sync //TODO socket 要呼叫的函式
  };
}
