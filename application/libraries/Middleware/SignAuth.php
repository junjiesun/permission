<?php
namespace Lib\Middleware;

use Kerisy\Core\MiddlewareContract;
use Kerisy\Log\Logger;


/**
 * BasicAccess middleware.
 *
 * @package Kerisy\Auth\Middleware
 */
class SignAuth implements MiddlewareContract
{
    /**
     *
     * @param Request $request
     */
    public function handle($controller)
    {
    	$user   = $controller->getUser();
    	if (in_array(request()->getRoute()->getAction(), $controller->notAuthActions()))
    	{
    		request()->abort = false;
    		return true;
    	}
    	else
    	{
    		if(empty($user))
    		{
    			if(request()->isAjax())
    			{
    				response()->json([
    					'httpStatusCode' => 403,
    					'message'        => '登录超时,请重新登录'
    					]);
    				request()->abort = true;
    				return false;
    			}else
    			{
    				response()->redirect('/signin');
    				request()->abort = true;
    				return false;
    			}

    		}
    		
    	}
    }
}
