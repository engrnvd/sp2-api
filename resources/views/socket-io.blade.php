<h1>Socket io test</h1>

<form id="form">
    {{ csrf_field() }}
    <input type="hidden" name="event" value="test-event">
    <p><input type="text" name="data" placeholder="data"></p>
    <button>Trigger</button>
</form>

<div id="messages"></div>

<script type="module">
    import { io } from "https://cdn.socket.io/4.4.1/socket.io.esm.min.js"

    const socket = io('{{ env('APP_URL') }}:{{ env('SOCKET_IO_PORT', '3210') }}')
    socket.onAny((eventName, ...args) => {
        console.log('eventName', eventName)
        console.log('args', args)
        const div = document.querySelector('#messages')
        const msg = document.createElement('p')
        msg.innerHTML = JSON.stringify(args)
        div.appendChild(msg)
    })
</script>

<script>
    const form = document.querySelector('#form')
    form.addEventListener('submit', e => {
        e.preventDefault()
        fetch('/socket-io', { method: 'POST', body: new FormData(form) })
    })
</script>
