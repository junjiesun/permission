<?php
namespace Lib\Controller;

use Kerisy\Http\Request;
use Kerisy\Http\Response;
use Kerisy\Http\Controller;
use Kerisy\Log\Logger;
use Lib\Services\UserCacheService;

class BaseController extends Controller
{
	private $notAuthActions = [];
    private $allowActions = [];

	private $userCacheService;

    public function __construct()
    {
        $this->middleware = [
                'Lib\Middleware\SignAuth',
                'Lib\Middleware\Auth'
            ];
        $this->userCacheService = new UserCacheService();
    }

    public function getUser()
    {
        $cookie = request()->cookie;
        $user = array();
        
        if ( array_key_exists("sid", $cookie) )
        {
            $user = session()->get($cookie['sid']);
            if(empty($user))
            {
                $key = $cookie['sid'];
                session()->destroy($key);
                response()->setCookie( 'sid', '', time() - 3600 );
                        
                if( !empty( session()->get($key) ) )
                {
                    return false;
                }
            }
        }
        return $user;
    }

    public function notAuthActions()
    {
        return $this->notAuthActions;
    }

    public function allowActions()
    {
        return $this->allowActions;
    }
	
	public function setNotAuthActions( Array $arr )
    {
        $this->notAuthActions = array_unique(array_merge($this->notAuthActions, $arr));
    }
	
	public function setAllowActions( Array $arr )
    {
        $this->allowActions = array_unique(array_merge($this->allowActions, $arr));
    }
	
	public function setCacheData( $key, $values, $expiration = null )
	{
		if ( empty($expiration) )
		{
			$expiration = time() + 3600 * 2;
		}
			
		return cache()->set($key, $values, $expiration);
	}
	
	public function getCacheData( $key )
	{
		return cache()->get($key);
	}
	
	public function deleteCacheData( $key )
	{
		return cache()->destroy($key);
	}

    public function menu()
    {
        $user = $this->getUser();
        $user['user_id'] = $user['userId'];
        $allowMenu = array();

        if($menuList = $this->userCacheService->menuCache((object)$user))
        {
            // $hideMenu = $this->hideMenuByRole($user['user_id']);
            
            foreach ($menuList as $menu)
            {
                // if(in_array($menu['controller'].'/'.$menu['method'] ,$hideMenu))
                // {
                //     continue;
                // }

                if(intval($menu['parent_menu_id']) === 0)
                {
                    $allowMenu[$menu['menu_id']] = $menu;
                }
                else
                {
                    if ( isset($allowMenu[$menu['parent_menu_id']]) )
                    {
                    	$allowMenu[$menu['parent_menu_id']]['submenu'][] = $menu;
                    }    
                }
            }
        }

        return $allowMenu;
    }

    public function getUserPermission()
    {

        $user = $this->getUser();
        $user['user_id'] = $user['userId'];
        $allowPermission = array();

        if($permissionList = $this->userCacheService->permissionCache((object)$user))
        {
            foreach ($permissionList as $permission)
            {
                if($permission->method)
                {
                    array_push($allowPermission, strtolower($permission->controller.'_'.$permission->method));
                }
            }
        }
        return $allowPermission;
    }

    public function initCA()
    {
        return strtolower(request()->getRoute()->getController() . '_' . request()->getRoute()->getAction());
    }
	
	public function error( int $number )
	{
		response()->redirect('/error/'. $number);
        request()->abort = true;
        return false;
	}

    // 设置隐藏菜单项
    public function hideMenuByRole($userId)
    {
        $hideMenuCM = array();
        if( $this->userCacheService->getAssetRole($userId) != 'MANAGER' )
        {
            $hideMenuCM[] = 'Asset/assetMoveList';
        }
        return $hideMenuCM;
    }
}
