$(function(){
    $('#click').keydown(function(event){
        if(event.keyCode == "13") {
            var content=$("#click").val()
            var id=$("#game_id").val();
            $.post("http://tp.swoole.com:8083/api/v1/chat" ,{id:id,content:content}, function (data) {

            }, 'json');
        }
    });
});

