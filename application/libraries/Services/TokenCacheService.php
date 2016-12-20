<?php
namespace Lib\Services;

use Kerisy\Log\Logger;

class TokenCacheService
{

	private $logService;

	public function __construct()
	{
		$this->logService = new Logger();
	}

    public function setTokenCache( String $key, String $values, Int $expiration = null )
	{
		if ( empty($expiration) )
		{
			$expiration = time() + 3600 * 2;
		}
			
		return cache()->set($key, $values, $expiration);
	}
	
	public function getTokenCache( String $key )
	{
		return cache()->get($key);
	}
	
	public function deleteTokenCache( String $key )
	{
		return cache()->destroy($key);
	}
}

