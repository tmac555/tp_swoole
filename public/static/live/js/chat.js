var id=getQueryVariable("game_id");
var wsServer = 'ws://192.168.21.8:8083?id='+id;
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {

    console.log("Connected to chat WebSocket server.");
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    chat_push(evt.data)
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};
function chat_push(data){
    chat_data=JSON.parse(data);
    html= '<div class="comment">';
    html+= '<span>'+chat_data.user_name+':</span>';
    html+= '<span>'+chat_data.content+'</span>';
    html+= '</div>';

$("#comments").prepend(html);
$("#click").val('');
}

