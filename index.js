var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');

var redis = new Redis();
redis.subscribe('App.User.1', function(err, count) {
});
redis.on('message', function(channel, message) {
    console.log('Notification Recieved: ' + message);
    message = JSON.parse(message);
    io.emit(channel + ':' + message.event, message.data);
});
http.listen(6001, function(){
    console.log('Listening on Port 3000');
});
