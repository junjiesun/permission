<?php
/**
 *
 *
 * @author          Kaihui Wang <hpuwang@gmail.com>
 * @copyright      (c) 2015 putao.com, Inc.
 * @since           16/7/8
 */
return [
        [
            "queue" => "clearlog",//job名称
            "sleep" => 1,//执行一次sleep多长时间
            "onlyOne"=>1,//是否只能插入一次数据
            "max_attempts" => 5,//失败后最多重试多少次
            "fail_on_output" => false//是否输出
        ],
    [
        "queue" => "heart",//job名称
        "sleep" => 1,//执行一次sleep多长时间
        "onlyOne"=>0,//是否只能插入一次数据
        "max_attempts" => 5,//失败后最多重试多少次
        "fail_on_output" => false//是否输出
    ],
];