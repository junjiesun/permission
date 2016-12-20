<?php
return [
    'class' => '\Kerisy\Rpc\Server\Swoole',
    'bootstrap' => require __DIR__ . '/../bootstrap.php',
    'host' => '0.0.0.0',
    'port' => 6000,
    'numWorkers' => 4,
    'maxRequests'=>20,
    'reactorNum'=>4,
    "logFile"=>"/tmp/rpcserver.log",
];
