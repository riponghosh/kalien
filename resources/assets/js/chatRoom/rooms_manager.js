//-----------------------------------------------------------------
// ● 多聊天室的管理物件
//-----------------------------------------------------------------
var roomsManager = new function(){
  var $container, roomsMapping = {}, roomLimit = 2;
  roomPosition = new Array(2);

  function destroy(roomID,room){
      room.destroySelf();
      delete roomsMapping[roomID];
    /*inline group移除*/
      roomPosition.forEach(function(item, index, object){
          if(item === roomID) object.splice(index, 1);
      })
  };
  return {
    initialize: function(_$container){
      $container = _$container;
    },
    create: function(roomID, currentUser, otherUser, options){
      if (roomsMapping[roomID] != undefined) return;
      //--------------------------
      // roomPosition
      //--------------------------
      if(options == null){
        if(roomPosition.length > roomLimit ) return;

      /*如果滿房，先移除*/
        if(roomPosition.length == roomLimit){
            var delRoomID = roomPosition.pop();
            var delRoom = roomsMapping[delRoomID]; //移除最左邊roomID且回傳給roomsMapping
            if(delRoom != undefined) delRoom.destroySelf();
            delete roomsMapping[delRoomID];
        }
      /*加入inline group*/
        roomPosition = [roomID, ...roomPosition];
      }
      //--------------------------
      // create room
      //--------------------------
      return roomsMapping[roomID] = createChatRoom($container, roomID, currentUser, otherUser, options,{
        destroyRoom: function () {
            roomsManager.destroy(roomID);
        }
      });
    },
    destroy: function(roomID){
      var room = roomsMapping[roomID];
      if(room == undefined) return;
      destroy(roomID,room);
    },
    sync: function(roomID, msgs){
      var room = roomsMapping[roomID]
      if (room != undefined) room.sync(msgs);
    }
  };
};
