var app = require('express');
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var Env = require('env.js');
var redis = new Redis({
                  host: '127.0.0.1',
                  password: Env.redisPassword,
                  family: 4,
                  port: 6379,
                  db: 0
                });
var redisSchedule = new Redis({
                  host: '127.0.0.1',
                  password: Env.redisPassword,
                  family: 4,
                  port: 6379,
                  db: 0     
});
var redisEventBlock = new Redis({
    host: '127.0.0.1',
    password: Env.redisPassword,
    family: 4,
    port: 6379,
    db: 0
});
var redisUserNotification = new Redis({
    host: '127.0.0.1',
    password: Env.redisPassword,
    family: 4,
    port: 6379,
    db: 0
});
/*
**使用者連接檢查
*/
io.on('connection', function(socket) {
  // 當使用者觸發 set-token 時將他加入屬於他的 room
  socket.on('set-token', function(token) {
    socket.join('token:' + token);
    console.log('connectAgain' + token);
  });
  // 當使用者離線時回傳
  socket.on('disconnect',function(socket){
    console.log('out');
  });
});
/*
 **通知
 */
redisUserNotification.on('message', function(channel, UserNotification) {
    // 當該頻道接收到訊息時就列在 terminal 上(測試用)
    console.log(UserNotification);
    UserNotification = JSON.parse(UserNotification);
    // 使用 to() 指定傳送的 room，也就是傳遞給指定的使用者
    io.to('token:' + UserNotification.data.send_to_user_token)
        .emit('user_notification', {
            title : UserNotification.data.title,
            body : UserNotification.data.body,
            icon: UserNotification.data.icon,
            is_read: UserNotification.data.is_read
        });
});
/*
**信息發送
*/
redis.on('message', function(channel, chatRoomMessage) {
  // 當該頻道接收到訊息時就列在 terminal 上(測試用)
  console.log(chatRoomMessage);
  chatRoomMessage = JSON.parse(chatRoomMessage);
   // 使用 to() 指定傳送的 room，也就是傳遞給指定的使用者
  io.to('token:' + chatRoomMessage.data.send_to_user_token)
    .emit('message_data', { 
                  content : chatRoomMessage.data.message.content,
                  chatroom_id : chatRoomMessage.data.room_id,
                  created_at : chatRoomMessage.data.message.created_at,
                  key : chatRoomMessage.data.message.key,
                  sent_by : chatRoomMessage.data.message.sent_by,
          });
});
/*
**行程表即時更新
*/
/******註：message是專用參數*****************/
redisSchedule.on('message', function(channel, scheduleEvent) {
  // 當該頻道接收到訊息時就列在 terminal 上(測試用)
  console.log(scheduleEvent);
  scheduleEvent = JSON.parse(scheduleEvent);
     // 使用 to() 指定傳送的 user，再判斷schedule_id,也就是傳遞給指定的使用者
  io.to('token:' + scheduleEvent.data.send_to_user_token)
    .emit('schedule_data', {
                  result : scheduleEvent.data.result,
                  schedule_id : scheduleEvent.data.schedule_id
          });
});
/*
 **行程表EventBlock即時更新
 */
/******註：message是專用參數*****************/
redisEventBlock.on('message', function(channel, scheduleEventBlockEvent) {
    // 當該頻道接收到訊息時就列在 terminal 上(測試用)
    console.log(scheduleEventBlockEvent);
    scheduleEventBlockEvent = JSON.parse(scheduleEventBlockEvent);
    // 使用 to() 指定傳送的 user，再判斷schedule_id,也就是傳遞給指定的使用者
    io.to('token:' + scheduleEventBlockEvent.data.send_to_user_token)
        .emit('eventBlock_data', {
            result : scheduleEventBlockEvent.data.result,
            schedule_id : scheduleEventBlockEvent.data.schedule_id
        });
});
/*
**初始化
*/
// 監聽 3000 port
http.listen(3000, function() {
  console.log('Listening on Port 3000');
});

// 訂閱 redis 的 notification 頻道，也就是我們在事件中 broadcastOn 所設定的
redis.subscribe('chatRoomMessage', function(err, count) {
  console.log('connect! chatroom');
});
redisSchedule.subscribe('scheduleEvent', function(err, count) {
  console.log('connect! schedule');
});
redisEventBlock.subscribe('scheduleEventBlockEvent', function(err, count) {
    console.log('connect! schedule eventblock');
});
redisUserNotification.subscribe('UserNotification', function (err, count) {
    console.log('connect! user Notification');
});

