var mysql = require('mysql');
var connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'login'
});
var fs = require('fs');
var socket = require('socket.io');
var http = require('http');
var https = require('https')
var express = require('express');
var app = express();
var server = http.createServer(app);
var io = require('socket.io').listen(server);

sessionid = 0
users = [];
connections = [];
player1 = ""
player2 = ""

server.listen(process.env.PORT || 3002);
console.log('SocketIO > listening on port');

app.get('../src', function (req, res) {
    res.sendFile(__dirname + '/home.php');
});


io.sockets.on('connection', function (socket) {
    var fs = require('fs');
    fs.appendFile("../tmp/log", "Log started!\n", function (err) {
        if (err) {
            return console.log(err);
        }
    });

    connections.push(socket);
    console.log("Connected: %s sockets connected", connections.length);
    fs.appendFile("../tmp/log", "1 node Connected!\n", function (err) {
        if (err) {
            return console.log(err);
        }
    });
    socket.on('disconnect', function (data) {
        users.splice(users.indexOf(socket.username), 1);
        updateUsernames();
        connections.splice(connections.indexOf(socket), 1);
        console.log("Disconnected: %s sockets connected", connections.length);
        fs.appendFile("../tmp/log", "1 node disconnected!\n", function (err) {
            if (err) {
                return console.log(err);
            }
        });
        player1 = ""
        player2 = ""
    });

    socket.on("send message", function (data) {
        console.log(data);
        io.sockets.emit("new message", {
            msg: data
        });
    });

    socket.on("new user", function (data) {
        fs.appendFile("../tmp/log", data + " Connected!\n", function (err) {
            if (err) {
                return console.log(err);
            }
        });
        socket.username = data;
        users.push(socket.username);
        updateUsernames();
    });

    function updateUsernames() {
        io.sockets.emit('get users', users);
    }
    socket.on('connection request', function (data) {
        io.sockets.emit('catch request', data);
    })

    socket.on('initial config', function (data, callback) {
        callback(true);
        console.log(data);
        sessionid += 1
        var q = "INSERT INTO moves VALUES('" + sessionid + "','" + data.Turn + "','" + data.Opponent + "','" + JSON.stringify(data.Board) + "')";
        console.log(q)
        connection.query(q, function (err, rows, fields) {
            if (err) throw err;
        });
        player1 = data.Turn;
        player2 = data.Opponent;
        io.sockets.emit('initial board', data);
    })
    socket.on('board config', function (data) {
        console.log("New board");
        insertVal = player1
        if (insertVal == data.Turn)
            insertVal = player2
        var q = "INSERT INTO moves VALUES('" + sessionid + "','" + data.Turn + "','" + insertVal + "','" + JSON.stringify(data.Board) + "')";
        console.log(q)
        connection.query(q, function (err, rows, fields) {
            if (err) throw err;
        });
        io.sockets.emit('catch board', data);
    })
    socket.on('Game End', function (data) {
        let playerWon
        let playerLost
        let totalwin = 0
        let totalloss = 0
        if (data == "red") {
            playerWon = player1
            playerLost = player2
        } else {
            playerWon = player2
            playerLost = player1
        }
        var r = "Select win from member where uname='" + playerWon + "'"
        connection.query(r, function (err, rows, fields) {
            if (err) throw err;
            console.log("TOTAL win", totalwin)
            totalwin = rows[0].win + 1
            console.log("TOTAL win", totalwin)
            var q = "UPDATE member SET win = " + totalwin + " WHERE uname='" + playerWon + "'";
            connection.query(q, function (err, rows, fields) {
                if (err) throw err;
                console.log("done")
            });

        });
        var v = "Select loss from member where uname='" + playerLost + "'"
        connection.query(v, function (err, rows, fields) {
            if (err) throw err;
            console.log("TOTAL loss", totalloss)
            totalloss = rows[0].loss + 1
            console.log("TOTAL loss", totalloss)
            var w = "UPDATE member SET loss = " + totalloss + " WHERE uname='" + playerLost + "'";
            connection.query(w, function (err, rows, fields) {
                if (err) throw err;
                console.log("done")
            });

        });
    })
    socket.on('Logout End', function (data) {
        console.log("LOGOUT HERE")
        let playerWon
        let playerLost
        let totalwin = 0
        let totalloss = 0
        if (player1) {
            if (data == player2) {
                playerWon = player1
                playerLost = player2
            } else {
                playerWon = player2
                playerLost = player1
            }
            var r = "Select win from member where uname='" + playerWon + "'"
            connection.query(r, function (err, rows, fields) {
                if (err) throw err;
                console.log("TOTAL win", totalwin)
                totalwin = rows[0].win + 1
                console.log("TOTAL win", totalwin)
                var q = "UPDATE member SET win = " + totalwin + " WHERE uname='" + playerWon + "'";
                connection.query(q, function (err, rows, fields) {
                    if (err) throw err;
                    console.log("HA")
                    io.sockets.emit("Game", data)
                });
            });
            var v = "Select loss from member where uname='" + playerLost + "'"
            connection.query(v, function (err, rows, fields) {
                if (err) throw err;
                console.log("TOTAL loss", totalloss)
                totalloss = rows[0].loss + 1
                console.log("TOTAL loss", totalloss)
                console.log(playerLost)
                var w = "UPDATE member SET loss = " + totalloss + " WHERE uname='" + playerLost + "'";
                connection.query(w, function (err, rows, fields) {
                    if (err) throw err;
                    console.log("done")
                });

            });
        } else {
            console.log("here tooooo")
            io.sockets.emit("Game", data)
        }
    })

});