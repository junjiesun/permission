<?php
/*
 * With reference to https://github.com/nrk/predis
 * With reference to https://www.sitepoint.com/an-introduction-to-redis-in-php-using-predis
 *
 * */
namespace Lib\Support\Redis;

use Predis\Client as RedisClient;
use Kerisy\Core\Object;

class Redis extends Object
{
    public $host = "127.0.0.1";
    public $port = 6379;
	public $scheme = 'tcp';

    protected $expire = 3600;

    private $redis;
	
	public function init()
	{
		if ( !in_array("redis", get_loaded_extensions()) )
		{
			throw new \Exception("not exist redis extensions error.");
		}
		
        // $this->redis = new \Redis();
        // $this->redis->connect($this->host, $this->port, $this->expire);
        $this->redis = new RedisClient([
		    'scheme' => $this->scheme,
		    'host'   => $this->host,
		    'port'   => $this->port,
		]);	

	}
	
	public function set($key, $value) 
	{
		$this->redis->set($key, $value);
	}
	
	public function get($key)
	{
		return $this->redis->get($key);
	}
	
	public function exists($key)
	{
		return ($this->redis->exists($key)) ? true : false;
	}

    public function lpush($key, $data)
    {
        $this->redis->lpush($key, $data);
    }

    public function rpush($key, $data)
    {
        $this->redis->rpush($key, $data);
    }

    public function llen($key)
    {
        return $this->redis->llen($key);
    }

    public function lrange($key, $end = -1, $start = 0)
    {
        return $this->redis->lrange($key, $start, $end);
    }

    public function ltrim($key, $start, $end)
    {
        return $this->redis->ltrim($key, $start, $end);
    }

    /*
    public function __call($method, $args)
    {
        $count = count($args);
        switch ($count) {
            case 1:
                $result = $this->redis->$method($args[0]);
                break;
            case 2:
                $result = $this->redis->$method($args[0], $args[1]);
                break;
            case 3:
                $result = $this->redis->$method($args[0], $args[1], $args[2]);
                break;
            case 4:
                $result = $this->redis->$method($args[0], $args[1], $args[2], $args[3]);
                break;
            case 5:
                $result = $this->redis->$method($args[0], $args[1], $args[2], $args[3], $args[4]);
                break;
            default:
                $result = false;
                break;
        }

        return $result;
    }
	*/
}
	