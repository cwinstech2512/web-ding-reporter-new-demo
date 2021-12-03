import Vue from "vue";
const wsUrl = process.env.VUE_APP_API_URL? process.env.VUE_APP_API_URL:'wss://front-webb.azurewebsites.net/ws/'
var socket = null
const emitter = new Vue({
  methods: {
    send(message) {
      if (socket && 1 === socket.readyState) socket.send(JSON.stringify(message))
    },
    connect() {
      socket = new WebSocket(wsUrl)
      socket.onmessage = function(msg) {
        console.log("onmessage")
        emitter.$emit("message", msg.data)
      };
      socket.onerror = function(err) {
        console.log("error")
        emitter.$emit("error", err)
      };
      socket.onclose = function() {
        console.log("close")
        emitter.connect()
      };
    }
  }
});

emitter.connect()
export default emitter