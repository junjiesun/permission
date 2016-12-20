<?php
namespace Lib\Services;
use \Lib\Database\DB;
use Kerisy\Log\Logger;
use Lib\Support\RouterHelper;

class UserCacheService
{
    private $routerService;
    private $expiration = 0;

    public function __construct()
    {
        $this->routerService = new RouterHelper();
        $this->expiration = time() + 3600 * 2;
    }

    public function permissionCache( $user )
    {

        if(empty($user) || empty($user->user_id)) return false;
        $permissionKey = sha1('user_permission'.$user->user_id.$user->name);
       
        if(!cache()->get($permissionKey))
        {
          $this->setPermissionCache($user);
        }
        return cache()->get($permissionKey);
    }
    
    public function setPermissionCache($user)
    {
        $permissionKey = sha1('user_permission'.$user->user_id.$user->name);
        cache()->set($permissionKey, $this->getUserPermission($user->user_id),$this->expiration);
    }

    public function menuCache($user)
    {
        if(empty($user)) return false;
        $menuKey = sha1('menu'.$user->user_id.$user->name);
        if(!cache()->get($menuKey))
        {
          $this->setMenuCache($user);
        }
        return cache()->get($menuKey);
    }

    public function setMenuCache($user)
    {
        if(empty($user)) return false;
        $menuKey       = sha1('menu'.$user->user_id.$user->name);
        cache()->set($menuKey, $this->getAllowMenu($user->user_id),$this->expiration);
    }

     public function getAllMenu()
    {
        $sql = "select m.name,m.menu_id,mr.parent_menu_id,mpr.permission_id from menu as m left join menu_rdd as mr
                on m.menu_id = mr.menu_id LEFT JOIN menu_permission_rdd as mpr
                ON mr.menu_id = mpr.menu_id where is_deleted = false and is_close = false order by mr.sort,mr.parent_menu_id,mr.menu_id";
        return DB::select($sql);
    }

    public function getUserPermission($userId)
    {
        $sql = "select ppgr.permission_id,p.controller,p.method,upgr.permission_group_id from user as u 
            left join user_permission_group_rdd as upgr ON u.user_id = upgr.user_id 
            left join permission_permission_group_rdd as ppgr ON upgr.permission_group_id = ppgr.permission_group_id 
            left join permission as p on ppgr.permission_id = p.permission_id
            where u.user_id = ?  and p.is_deleted = false ORDER BY ppgr.permission_id";
        $userPermission = DB::select($sql, [$userId]);

        $userPermission = $this->doDiffUserPermission($userPermission, $userId);
        
        return $userPermission;
    }

    /**
     * [doDiffUserPermission  以权限组内的用户权限为主]
     * @param  Array  $groupPermission [description]
     * @param  int    $userId          [description]
     * @return [type]                  [description]
     */
    public function doDiffUserPermission(Array $groupPermission, int $userId)
    {
        $sql = 'select permission_group_id, permission_id from permission_group_user_permission_rdd where user_id = ?';
        $list = DB::select($sql, [$userId]);
        if(empty($list)) return $groupPermission;

        //用户在权限组内的权限
        $userGroupPermission = array();
        foreach ($list as $row) 
        {
            $userGroupPermission[$row->permission_group_id][] = $row->permission_id;
        }

        foreach ($groupPermission as $key => $permission) 
        {
            if(array_key_exists($permission->permission_group_id, $userGroupPermission))
            {
                if(!in_array($permission->permission_id, $userGroupPermission[$permission->permission_group_id]))
                {
                    unset($groupPermission[$key]);
                }
            }
        }
        return $groupPermission;
    }

    public function getAllowMenu($userId)
    {
        $menuList       = $this->getAllMenu();
//        var_dump($menuList);
        $userPermission = $this->getUserPermission($userId);

        $userAllPerm = array();
        foreach ($userPermission as  $permission) 
        {
            $userAllPerm[$permission->permission_id] = (Array)$permission;
        }

        $allowMenu = array();
        foreach ($menuList as  $menu)
        {
            if(array_key_exists($menu->permission_id, $userAllPerm) && isset($userAllPerm[$menu->permission_id]))
            {
                $route = array('controller'=>$userAllPerm[$menu->permission_id]['controller'],'action'=>$userAllPerm[$menu->permission_id]['method']);
//                var_dump($route);
                $menuUrl = $this->routerService->getUrlByRoute($route);
                $menu->menuUrl = $menuUrl;

                array_push($allowMenu, array_merge((Array)$menu,$userAllPerm[$menu->permission_id]));
            }
        }
        return $allowMenu;
    }

    /**
     * [updatePermissionCache 更新整个权限组内的用户权限、菜单缓存]
     * @param  [type] $permissionGroupId [description]
     * @return [type]                    [description]
     */
    public function updatePermissionCache($permissionGroupId)
    {
        $sql = "select u.user_id,u.name from user_permission_group_rdd as upgr left join user as u
                on upgr.user_id = u.user_id where u.is_deleted = false and u.can_login = true 
                and upgr.permission_group_id = ?";

        $permissionUser = DB::select($sql,[$permissionGroupId]);
        //update user cache
        foreach ($permissionUser as $user) 
        {
            $this->clearUserCache($user);
            // $this->setPermissionCache($user);
            // $this->setMenuCache($user);
        }
    }
    
    public function cachePermgroupAllUser()
    {
        $key = sha1('alluserpermissonGroup');
        $allUser = cache()->get($key);
        if(!$allUser)
        {
            $this->setCacheAllUserByType('permissonGroup');
            return cache()->get($key);
        }
        return $allUser;
    }

    public function setCachePermgroupAllUser()
    {
        $this->setCacheAllUserByType('permissonGroup');
    }

    public function cacheAllUser()
    {
        $key = sha1('alluser');
        $allUser = cache()->get($key);
        if(!$allUser)
        {
            $this->setCacheAllUserByType();
            return cache()->get($key);
        }
        return $allUser;
    }

    public function setCacheAllUser()
    {
        $this->setCacheAllUserByType();
    }


    public function setCacheAllUserByType($type = '')
    {
        $key = sha1('alluser'.$type);
        $allUser  = $this->getAlluser($type);
        cache()->set($key, $allUser,$this->expiration);
    }

    public function getAlluser($type = '')
    {
        $where = $type != 'permgroup' ? ' and type = "USER" ' : 'and type !="SUPER_ADMIN"';
        $sql = 'select user_id,name,email from user where is_deleted = false and can_login = true '.$where;
        return DB::select($sql);
    }
    
    /**
     * [clearAll 清除所有缓存，包涵系统缓存]
     * @param  [type] $user [description]
     * @return [type]       [description]
     */
    public function clearAll($user)
    {
        $exp = time()-3600;
        $permissionKey = sha1('user_permission'.$user->user_id.$user->name);
        cache()->set($permissionKey, '', $exp);

        $menuKey = sha1('menu'.$user->user_id.$user->name);
        cache()->set($menuKey, '', $exp);

        cache()->set(sha1('alluserpermissonGroup'), '', $exp);
        cache()->set(sha1('alluser'), '', $exp);
    }

    /**
     * [clearUserCache 清除用户缓存]
     * @param  [type] $user [description]
     * @return [type]       [description]
     */
    public function clearUserCache($user)
    {
        $exp = time()-3600;
        $permissionKey = sha1('user_permission'.$user->user_id.$user->name);
        cache()->set($permissionKey, '', $exp);

        $menuKey = sha1('menu'.$user->user_id.$user->name);
        cache()->set($menuKey, '', $exp);
    }

    /**
     * [setUserCache 设置用户缓存，（权限和菜单）]
     * @param int $userId [description]
     */
    public function setUserCache(int $userId)
    {
        $user = DB::selectOne("select user_id,name from user where user_id = ?",[$userId]);
        $this->setPermissionCache($user);
        $this->setMenuCache($user);
    }
    
    public function getAssetRole($userId)
    {
        $returnRole = '';
        $sql = "select role from asset_user_role where user_id = ? and is_deleted = FALSE ";
        $result = DB::select($sql,[$userId]);
        if(count($result) > 0)
        {
            $returnRole = $result[0]-> role;
        }
        return $returnRole ;
    }
}

