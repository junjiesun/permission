<?php

return [
    'template_engine' => "blade",
    "monitor" => [
        "name" => "putao_permission",
        'server' => ["host" => "127.0.0.1",
            "port" => "7777"
        ],
    ],
    "monitor_servers" => [

    ],
    "monitor_log_path"=> APPLICATION_PATH . "runtime/monitor/",
    'cookie_domain' => 'ldev.admin-operating-activities.putao.com',
//    'cookie_domain' => 'admin-operating-activities.ptdev.cn',
    "menu"=>[
        "index/monitor/view"=>"index/monitor/index",
        "index/heart/add"=>"index/heart/index",
        "index/heart/loglist"=>"index/heart/index",
    ],
    "monitor_heart_group"=>[

    ],
];