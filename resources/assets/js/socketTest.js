var io = require('socket.io-client');
var env = require('public_env');
// 建立 socket.io 的連線
var notification = io.connect(env.socketURL);

// 當從 socket.io server 收到 notification 時將訊息印在 console 上
notification.on('connect',function(){
  notification.emit('set-token', Notification.TOKEN);
});
if (typeof roomsManager != 'undefined'){
  notification.on('message_data', function(data){
    var msg = {content: data.content, key: data.key, created_at: data.created_at, sent_by: data.sent_by};
    roomsManager.sync(data.chatroom_id, [msg])
  });
}
if (typeof schedularsManager != 'undefined'){
  notification.on('eventBlock_data', function(response){
    schedularsManager.syncEvent(response.schedule_id, response.result);
  });
  notification.on('schedule_data', function(response){
    schedularsManager.syncSchedule(response.schedule_id, response.result);
  });
}
//==========================================
//  user notification
//==========================================
notification.on('user_notification', function(response) {
    userNotificationsManager.create(response);
});
