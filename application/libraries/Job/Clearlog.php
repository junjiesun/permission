<?php
/**
 *
 *
 * @author          Kaihui Wang <hpuwang@gmail.com>
 * @copyright      (c) 2015 putao.com, Inc.
 * @since           16/7/11
 */
namespace Lib\Job;

class Clearlog
{
    
    public function perform() {
        $obj = new \App\Index\Service\Analyse();
        $obj->clearLog();
    }
}
