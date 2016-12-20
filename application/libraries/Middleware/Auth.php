<?php
namespace Lib\Middleware;

use Kerisy\Core\MiddlewareContract;
use Kerisy\Log\Logger;


/**
 * BasicAccess middleware.
 *
 * @package Kerisy\Auth\Middleware
 */
class Auth implements MiddlewareContract
{
    /**
     *
     * @param Request $request
     */
    public function handle($controller)
    {
        $currentCM = $controller->initCA();
        $user = $controller->getUser();

        if(!empty($user))
        {
            $notAuth = array_merge($controller->allowActions(), $controller->notAuthActions());

            if(in_array($currentCM, $controller->getUserPermission()) == false
              && in_array(request()->getRoute()->getAction(), $notAuth) == false)
            {
                if(request()->isAjax())
                {
                    response()->json([
                      'httpStatusCode' => 403,
                      'message'        => '您没有该操作的权限'
                      ]);
                    request()->abort = true;
                    return false;
                }else
                {
                    response()->redirect('/');
                    request()->abort = true;
                    return false;
                }
            }

            response()->assign('menuList',$controller->menu());
            response()->assign('currentCM',$currentCM);
            response()->assign('user',$user);
        }
        
    }
}
