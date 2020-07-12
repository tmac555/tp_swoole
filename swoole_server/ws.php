<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/8 0008
 * Time: 14:40
 */

use think\app;
use app\common\lib\redis\Predis;

class ws
{
    const HOST = "0.0.0.0";
    const PORT = 9501;
    CONST CHAT_PORT = 8083;
    public $ws = null;

    public function __construct()
    {
        $this->ws = new Swoole\WebSocket\Server(self::HOST, self::PORT);
        $this->ws->listen(self::HOST, self::CHAT_PORT, SWOOLE_SOCK_TCP);
        $this->ws->set([
            'enable_static_handler' => true,
            'document_root' => "/www/tp/public/static",
            'worker_num' => 5,
            'task_worker_num' => 4,
        ]);

        $this->ws->on("start", [$this, 'onStart']);
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('workerstart', [$this, 'onWorkstart']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on('request', [$this, 'onRequest']);
        $this->ws->on('close', [$this, 'onClose']);
        $this->ws->start();
    }

    /**
     * @param $ws
     */
    public function onStart($ws)
    {
        swoole_set_process_name("live_master");
    }

    /**
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request)
    {
        if (!empty($request->get['id'])) {
            //加入房间分组集合
            Predis::getInstance()->sAdd('live_group', \Yaconf::get("redis.live_game_room_key") . $request->get['id']);
            //加入分组
            Predis::getInstance()->Hset(\Yaconf::get("redis.live_game_room_key") . $request->get['id'], \Yaconf::get("redis.live_game_room_key") . $request->fd, $request->fd);
        }
        var_dump($request->fd);
    }

    /**
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame)
    {
        echo $frame->data;
        $ws->push($frame->fd, "this is server");

    }

    /**
     * @param $ws
     * @param $worker_id
     */
    public function onWorkstart($ws, $worker_id)
    {
        require __DIR__ . '/../vendor/autoload.php';
    }

    /**
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        if ($request->server['request_uri'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            $response->status(404);
            $response->end();
            return;
        }

        if (isset($request->server)) {
            foreach ($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if (isset($request->header)) {
            foreach ($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        $_GET = [];
        if (isset($request->get)) {
            foreach ($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        $_FILES = [];
        if (isset($request->files)) {
            foreach ($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }
        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

        $_POST['ws_server'] = $this->ws;
        ob_start();
        // 执行应用并响应
        try {
            (new App())->http->run()->send();
        } catch (\Exception $e) {
            // todo
        }

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($ws, $taskId, $workerId, $data)
    {
        // 分发 task 任务机制，让不同的任务 走不同的逻辑
        $obj = new \app\common\lib\task\Task;

        $method = $data['method'];
        $flag = $obj->$method($data['data'], $ws);
        return $flag; // 告诉worker
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($ws, $taskId, $data)
    {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    /**
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        //查出分组集合
        $group = Predis::getInstance()->sMembers('live_group');
        foreach ($group as $value) {
            //分组
            $list = Predis::getInstance()->hGetALL($value);
            foreach ($list as $key => $val) {
                if ($val == $fd) {
                    Predis::getInstance()->hDel($value,$key);
                }
            }
        }
        echo "close:{$fd}\n";

    }


}

new ws();