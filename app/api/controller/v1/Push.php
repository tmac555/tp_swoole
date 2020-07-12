<?php

namespace app\api\controller\v1;

use app\BaseController;
use think\Request;

class Push extends BaseController
{
    public function index(){
      dump(\Yaconf::get("redis.host"));
        return 123;
    }

    public function push(Request $request)
    {

        // => mysql
        $teams = [
            1 => [
                'name' => '广东马可波罗',
                'logo' => './imgs/gddg.png',
            ],
            4 => [
                'name' => '广东宏远',
                'logo' => './imgs/gdhy.png',
            ],
        ];
        $team_id = $request->param('team_id');
        $data = [
            'type' => $request->param('type'),
            'title' => !empty($teams[$team_id]['name']) ? $teams[$team_id]['name'] : '直播员',
            'logo' => !empty($teams[$team_id]['logo']) ? $teams[$team_id]['logo'] : '',
            'content' => !empty($request->param('content')) ? $request->param('content') : '',
            'time' => !empty($request->param('time')) ? $request->param('time') : 0,
            'homescope' => !empty($request->param('homescope')) ? $request->param('homescope') : 0,
            'awayscope' => !empty($request->param('awayscope')) ? $request->param('awayscope') : 0,
            'room_id' => !empty($request->param('room')) ? $request->param('room') : 0,
           // 'image' => !empty($_POST['image']) ? $_POST['image'] : '',
        ];


        $taskData = [
            'method' => 'pushLive',
            'data' => $data
        ];

        $_POST['ws_server']->task($taskData);
        return show(1, 'ok', $data);
    }

}
