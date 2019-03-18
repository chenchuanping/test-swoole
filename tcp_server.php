<?php

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 9501, SWOOLE_SOCK_TCP);

$serv->set(array(
    'worker_num' => 2,
    'daemonize' => false,
    'max_request' => 10000,
    'dispatch_model' => 2,
));

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
    echo "Client {$fd}: Connect.\n";
});

//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    echo "等待10秒后再返回客户端{$fd}\n";
    sleep(10);
    $serv->send($fd, "Server{$from_id}返回客户端{$fd}，数据： " . $data . "\n");
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client{$fd}: Close.\n";
});

//启动服务器
$serv->start();