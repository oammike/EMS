
var config = require('./config.js');
var tools = require('./tools.js');

const express = require('express');
const app = express();
const path = require('path');
const server = require('http').createServer(app);
const io = require('socket.io')(server, {
  cors:{
    origin: config.general.cors,
    methods: ["GET","POST"]
  }
});
const port = config.general.port;

server.listen(port, () => {
  console.log('Server listening at port %d', port);
});

app.use(express.static(path.join(__dirname, 'public')));

io.on("connect_error", (err) => {
  console.log(`connect_error due to ${err.message}`);
});

io.on('connection', function (socket) {
    let id = 0;

    socket.on('set_id', (user_id) => {
      if (id!=0) return;
      socket.id = user_id;
      id = user_id;
      console.log('user connected. ID: '+socket.id);
    });

    socket.on('send', function (message) {

        if (config.general.debug) {
          console.log('received event: ' + message.event);
        }

        io.sockets.emit("event", {
            data: message,
            emmitter: socket.id
        });
    });

    socket.on('disconnect', function () {
      console.log('User with ID:'+socket.id+' disconnected');
    });

});

