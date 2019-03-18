<?php

$db = new Swoole\MySQL;
$server = array(
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => 'chen891105',
    'database' => 'test',
);

$db->on('close', function () {
    echo 'Mysql closed' . PHP_EOL;
});

$db->connect($server, function ($db, $result) {
    $db->query("show tables", function ($db, $result) {
        var_dump($result);
        $db->close();
    });
    var_dump($result);
});