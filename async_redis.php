<?php

$redis = new Swoole\Redis;

$redis->on('close', function () {
    echo 'Redis closed' . PHP_EOL;
});

$redis->connect('127.0.0.1', 6379, function ($redis, $result) {
    $redis->set('test_key', 'value', function ($redis, $result) {
        var_dump($result);
        $redis->get('test_key', function ($redis, $result) {
            var_dump($result);
            $redis->mset('a', 1, 'b', 2, function ($redis, $result) {
                var_dump($result);
                $redis->mget('a', 'b', 'test_key', function ($redis, $result) {
                    var_dump($result);
                    $redis->close();
                });
            });
        });
    });
    var_dump('connect' . $result);
});

