require('dotenv').config({ path: '../.env' })
let server
let ioOptions = {
    cors: { origin: true }
}
if (process.env.APP_ENV === 'production') {
    const fs = require('fs')
    server = require('https').Server({
        key: fs.readFileSync(process.env.CERTS_FILE_KEY),
        cert: fs.readFileSync(process.env.CERTS_FILE),
        ca: fs.readFileSync(process.env.CERTS_FILE_CHAIN),
        requestCert: false,
        rejectUnauthorized: false
    })
    ioOptions.cors.origin = [process.env.FRONTEND_URL, process.env.ADMIN_URL]
} else {
    server = require('http').Server()
}

const { Server } = require("socket.io")
const io = new Server(server, ioOptions)
const Redis = require('ioredis')
const axios = require("axios")
const redis = new Redis(`redis://${process.env.REDIS_HOST}:${process.env.REDIS_PORT}`)

const redisChannel = process.env.SOCKET_IO_CHANNEL || 'socket-io'
redis.subscribe(redisChannel, (err, channelsCount) => {
    if (err) console.error("Failed to subscribe to redis: %s", err.message)
    else console.log(`Subscribed successfully to redis`)
})

redis.on('message', function (channel, message) {
    message = JSON.parse(message)
    const _io = message.room ? io.to(message.room) : io
    _io.emit(message.event, message.data)
})

io.on('connection', function (socket) {
    const token = socket.handshake.auth.token
    if (token) {
        axios.get(`http://laravel.app:8000/user`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }).then(res => {
            socket.join(`${process.env.SOCKET_IO_USER_ROOM}-${res.data.id}`)
        }).catch(res => {
            console.log('error', res)
        })
    } else {
        socket.join(process.env.SOCKET_IO_PUBLIC_ROOM)
    }

    socket.on("error", (err) => {
        console.log('err', err)
        if (err && err.message === "unauthorized") {
            socket.disconnect()
        }
    })

    socket.on('disconnecting', (reason) => {
        socket.emit('disconnected', { pid: socket.id, referer: socket.handshake.headers.referer })
    })
})

const port = process.env.SOCKET_IO_PORT || 3210

server.listen(port, function () {
    console.log(`Node server is running socket.js on port ${port}`)
})
