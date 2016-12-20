<?php
namespace App\Core\Services;

use Lib\Database\DB;
use Kerisy\Log\Logger;
use Lib\Services\UserCacheService;
use Lib\Support\BaseUtil;

class SystemService
{
	private $logService;
	private $publicService;
	private $userCacheService;

	public function __construct(Logger $logger, PublicService $publicService, UserCacheService $userCacheService)
	{
		$this->logService = $logger;
		$this->publicService = $publicService;
		$this->userCacheService = $userCacheService;
	}


}



