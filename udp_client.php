<?php


$client = new swoole_client(SWOOLE_SOCK_UDP);

if (!$client->connect('127.0.0.1', 9502, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}
$data = isset($argv[1]) ? $argv[1] : 'hello world';
$client->send("{$data}\n");
echo $client->recv();
$client->close();