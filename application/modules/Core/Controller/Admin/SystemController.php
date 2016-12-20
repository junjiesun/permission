<?php

namespace App\Core\Controller\Admin;

use Kerisy\Http\Request;
use Kerisy\Http\Response;
use Kerisy\Log\Logger;
use Lib\Controller\BaseController;
use App\Core\Services\SystemService;
use Lib\Support\Paginate;
use Lib\Services\UserCacheService;

class SystemController extends BaseController
{
    private $logService;
    private $systemService;
    private $userCacheService;

    public function __construct(
    	Logger $logger,
    	SystemService $systemService,
    	UserCacheService $userCacheService
	)
    {
        parent::__construct();
        $this->logService = $logger;
        $this->systemService = $systemService;
        $this->userCacheService = $userCacheService;
        $this->setAllowActions(['getChildRelation','doClearCache']);
    }

    //缓存清理
    public function clearCache(Request $request, Response $response)
    {
        return $response->view('system/clearcache');
    }

    public function doClearCache(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $user = $this->getUser();
        $user['user_id'] = $user['userId'];
        $this->userCacheService->clearAll((object)$user);
        if($request->isAjax())
        {
            return $response->json([
                'httpStatusCode' => $httpStatusCode
            ]);
        }
        else
        {
            $response->redirect('/');
            request()->abort = true;
            return false;
        }
    }


	
}
