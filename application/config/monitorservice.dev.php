<?php
return [
    'class' => '\Kerisy\Monitor\Server',
    'bootstrap' => require __DIR__ . '/../bootstrap.php',
    'host' => '0.0.0.0',
    'port' => 7777,
    'numWorkers' => 12,
    'maxRequests'=>20,
    'reactorNum'=>4,
    "logFile"=>"/tmp/monitorserver.log",
];
