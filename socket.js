var app = require('express');
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis({
                  host: '127.0.0.1',
                  family: 4,
                  port: 6379,
                  db: 0
                });
redis.set('dage','23');
redis.get('name',function (err, result) {
  console.log(result); 
});
redis.subscribe('notification', function(err, count) {
  console.log('connect!');
});

io.on('connection', function(socket) {
  // 當使用者觸發 set-token 時將他加入屬於他的 room
  socket.on('set-token', function(token) {
    socket.join('token:' + token);
    console.log('connectAgain');
    redis.get('name',function (err, result) {
  console.log(result); 
});
  });
  // 當使用者離線時回傳
  socket.on('disconnect',function(socket){
    console.log('out');
  });
});

redis.on('message', function(channel, notification) {
  console.log(notification);
  notification = JSON.parse(notification);
   // 使用 to() 指定傳送的 room，也就是傳遞給指定的使用者
  io.to('token:' + notification.data.token).emit('notification', notification.data.message);
});

// 監聽 3000 port
http.listen(3000, function() {
  console.log('Listening on Port 3000');
});