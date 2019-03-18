<?php

class MySQLPool
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server('127.0.0.1', 9501);

        $this->serv->set(array(
            'worker_num' => 4,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_model' => 3,
            'debug_mode' => 1,
            'task_worker_num' => 4
        ));

        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }

    public function onConnect($erv, $fd, $from_id)
    {
        echo "Client {$fd} connect\n";
    }

    public function onClose($serv, $fd, $from_id)
    {
        echo "Client {$fd} close connection\n";
    }

    public function onWorkerStart($serv, $worker_id)
    {
        echo "onWorkerStart\n";
        if ($serv->taskworker) {
            $this->pdo = new PDO(
                'mysql:host=localhost;port=3306;dbname=test',
                'root',
                'chen891105',
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8';",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true
                )
            );
        } else {
            echo "Worker Process\n";
        }
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        var_dump($data);
        $task = [
            'sql' => 'insert into user_info values (?, ?)',
            'params' => [3, 'chenshiying'],
            'fd' => $fd
        ];
        $this->serv->task(json_encode($task));
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        try {
            $data = json_decode($data, true);
            $statement = $this->pdo->prepare($data['sql']);
            $statement->execute($data['params']);

            $serv->send($data['fd'], "Insert success\n");
            return true;
        } catch (PDOException $exception) {
            var_dump($exception);
            return false;
        }
    }

    public function onFinish($serv, $task_id, $data)
    {
        var_dump('Result: ', $data);
    }
}

$server = new MySQLPool();
