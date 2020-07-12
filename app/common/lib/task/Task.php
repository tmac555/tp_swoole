<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/9 0009
 * Time: 20:26
 */

namespace app\common\lib\task;

use app\common\lib\redis\Predis;
class Task
{
    public function pushLive($data, $serv) {
        $list=Predis::getInstance()->hGetALL(\Yaconf::get("redis.live_game_room_key").$data['room_id']);
        foreach($serv->ports[0]->connections as $fd) {
            if(in_array($fd,$list)) {
                $serv->push($fd, json_encode($data));
            }
        }

    }
}