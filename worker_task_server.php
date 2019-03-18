<?php

$serv = new swoole_server('127.0.0.1', 9501, SWOOLE_SOCK_TCP);

// 设置异步任务的工作进程数量
$serv->set(array(
    'worker_num' => 1,
    'task_worker_num' => 2
));

$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    // 投递异步任务
    $task_id = $serv->task($data);
    echo "Dispatch AsyncTask: id={$task_id}\n";
    $serv->send($fd, "Server Dispatch AsyncTask: id={$task_id}\n");
});

// 处理异步任务
$serv->on('Task', function ($serv, $task_id, $from_id, $data) {
    echo "New AsyncTask[id={$task_id}]" . PHP_EOL;
    sleep(5);
    // 返回异步执行的结果
    $serv->finish("{$data} -> OK");
});

// 处理异步任务的结果
$serv->on('Finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[{$task_id}] finish: {$data}" . PHP_EOL;
});

$serv->start();