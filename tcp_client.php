<?php

$client = new swoole_client(SWOOLE_SOCK_TCP);

// connect的1代表的是整个与服务端交互的超时时间,超过这个时间会自动断开
if (!$client->connect('127.0.0.1', 9501, -1)) {
    die('connect failed.');
}
$str = isset($argv[1]) ? $argv[1] : 'hello world';

if (!$client->send($str)) {
    die('send failed.');
} else {
    echo 'send success' . PHP_EOL;
}
// 从服务器接收数据,如果是超时的错误,这种方式捕获不了,会报PHP Warning
if (!$data = $client->recv()) {
    die('recv failed.');
}
echo $data;
// 关闭连接
$client->close();

