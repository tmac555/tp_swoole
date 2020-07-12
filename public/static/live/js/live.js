var id=getQueryVariable("game_id");
var wsServer = 'ws://192.168.21.8:9501?id='+id;
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
     push(evt.data)
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};
function push(data) {
    data=JSON.parse(data);
    html= '<div class="frame">';
    html+= '<h3 class="frame-header">';
    html+= '<i class="icon iconfont icon-shijian"></i>第'+data.type+'节 '+data.time+'';
    html+= '</h3>';
    html+= '<div class="frame-item">';
    html+= '<span class="frame-dot"></span>';
    html+= '<div class="frame-item-author">';
    html+= '<img src="'+data.logo+'" width="20px" height="20px" /> '+data.title+'';
    html+= '</div>';
    html+= '<p>'+data.content+'</p>';
    html+= '</div>';
    html+= '</div>';

$("#match-result").prepend(html);
$("#home_scope").html(data.homescope);
$("#away_scope").html(data.awayscope);
}
function getQueryVariable(variable)
{
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}