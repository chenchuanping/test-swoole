<?php

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);


// 注册连接成功回调
$client->on('connect', function ($cli) {
    echo "Client 连接成功，并发送数据 hello world\n";
    $cli->send('hello world');
});

// 注册数据回收回调
$client->on('receive', function ($cli, $data) {
    echo "Client 接收数据：{$data}\n";
    sleep(2);
    echo "2秒后关闭连接\n";
    $cli->close();
});

// 注册连接失败回调
$client->on('error', function ($cli) {
    echo "Client 连接失败\n";
});

// 注册连接关闭回调
$client->on('close', function ($cli) {
    echo "Client 关闭连接\n";
});

// 发起连接
$client->connect('127.0.0.1', 9501, 0.5);
sleep(10);
echo "异步执行\n";