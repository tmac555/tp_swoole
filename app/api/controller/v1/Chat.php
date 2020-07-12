<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/10 0010
 * Time: 19:05
 */

namespace app\api\controller\v1;


use app\BaseController;
use app\Request;

class Chat extends BaseController
{
    public function post(Request $request)
    {
        $content=$request->param('content');
        $id=$request->param('id');
       if(empty($content)){
          return show(0,'error');
       }
        $data = [
            'user_name' => "用户".rand(0, 2000),
            'content' => $content,
        ];
        //  todo
        $list=\app\common\lib\redis\Predis::getInstance()->hGetALL(\Yaconf::get("redis.live_game_room_key").$id);
        foreach($_POST['ws_server']->ports[1]->connections as $fd) {
            if(in_array($fd,$list)) {
                $_POST['ws_server']->push($fd, json_encode($data));
            }
        }
        return show(1,'ok', $data);

    }
}