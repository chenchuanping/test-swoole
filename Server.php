<?php

class Server
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server('127.0.0.1', 9501);
        $this->serv->set(array(
            'worker_num' => 4,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_model' => 1,
            'task_worker_num' => 4
        ));


        $this->serv->on('Start', array($this, 'onStart'));

        $this->serv->on('Connect', array($this, 'onConnect'));

        $this->serv->on('Receive', array($this, 'onReceive'));

        $this->serv->on('Close', array($this, 'onClose'));

        $this->serv->on('Task', array($this, 'onTask'));

        $this->serv->on('Finish', array($this, 'onFinish'));

        $this->serv->start();
    }

    public function onStart($serv)
    {
        echo "Start\n";
    }

    public function onConnect($serv, $fd, $from_id)
    {
        echo "Client {$fd} connect\n";
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        echo "Get message from client {$fd}:{$data}\n";

        $this->serv->send($fd, "Server{$from_id}返回客户端{$fd}，数据： " . $data . "\n");
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo "Client {$fd} close connection\n";
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        echo "This Task {$task_id} from Worker {$from_id}\n";
        echo "Data: {$data}\n";
    }

    public function onFinish($serv, $task_id, $data)
    {
        echo "Task {$task_id} finish\n";
        echo "Result: {$data}";
    }
}

$server = new Server();
