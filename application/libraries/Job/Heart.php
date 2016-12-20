<?php
/**
 *  心跳检测
 *
 * @author          Kaihui Wang <hpuwang@gmail.com>
 * @copyright      (c) 2015 putao.com, Inc.
 * @since           16/7/11
 */
namespace Lib\Job;

class Heart
{
    //心跳记录id
    private $id = null;

    /**
     * Heart constructor.
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * 执行
     */
    public function perform()
    {
        if (!$this->id) return;

        $obj = new \App\Index\Service\Heart();
        $obj->runHeartJob($this->id);
    }
}
