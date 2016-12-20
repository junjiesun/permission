<?php
/*	
 * With reference to https://github.com/overtrue/pinyin
 * 
 * */
namespace Lib\Support\Pinyin;

use Overtrue\Pinyin\Pinyin as chToPinyin;

class Pinyin extends chToPinyin
{
	
	public function __construct($memoryFileDictLoader = false)
    {
        if($memoryFileDictLoader)
        {
            parent::__construct('Overtrue\Pinyin\MemoryFileDictLoader');
        }else{
            parent::__construct();
        }
    }
		
	public function __call($method, $parameters)
	{
		return call_user_func_array([parent, $method], $parameters);
	}

}
/**
    PINYIN_NONE 不带音调输出: mei hao
    PINYIN_ASCII    带数字式音调： mei3 hao3
    PINYIN_UNICODE  UNICODE 式音调：měi hǎo
 */