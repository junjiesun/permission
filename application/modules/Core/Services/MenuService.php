<?php
namespace App\Core\Services;

use Lib\Database\DB;
use Kerisy\Log\Logger;
// use Lib\Support\Ptldap\Ptldap;
use Lib\Support\User;
use Lib\Services\UserCacheService;

class MenuService
{
	private $logService;
	private $menuTreeHtml;
	private $userCacheService;

	public function __construct(Logger $logger, UserCacheService $userCacheService)
	{
		$this->logService = $logger;
		$this->userCacheService = $userCacheService;
	}

	public function getMenuList()
	{
		$sql = 'select m.menu_id,m.name,m.description,is_close, mr.parent_menu_id,mr.sort from menu as m
				left join menu_rdd as mr on m.menu_id = mr.menu_id where is_deleted = false order by mr.sort, mr.parent_menu_id,mr.menu_id';
        $data = DB::select($sql);
        $menuList = array();
        foreach ($data as $menu) 
        {
        	if(intval($menu->parent_menu_id) === 0)
	        {
	            $menuList[$menu->menu_id] = (array)$menu;
	            $menuList[$menu->menu_id]['sub'][0] = (array)$menu;
	        }
	        else
	        {
	            if (isset($menuList[$menu->parent_menu_id]))
	            {
	            	$menuList[$menu->parent_menu_id]['sub'][] = (array)$menu;
	            }    
	        }
        }
        return $menuList;
	}

	public function getAllMenu()
	{
		$sql = 'select menu_id,name from menu where is_deleted = false';
		$allMenu = DB::select($sql);
		
		$allMenuList = array();
		foreach ($allMenu as $menu) 
		{
			$allMenuList[$menu->menu_id] = $menu->name;
		}

		return $allMenuList;
	}
	public function menuAddPost(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		$parent_id     = $parameters['parent_id'];
		$name          = $parameters['name'];
		$permission_id = $parameters['perm_id'];
		$description   = $parameters['description'];
		$sort          = intval($parameters['sort']);
		$icon          = $parameters['icon'];
		$status        = intval($parameters['status']);
		
		$parentMenu = $this->getMenuSort($parent_id);
		try 
		{
			$time = time();
			DB::beginTransaction();
			$menu  = [
				'name'        => $name,
				'is_deleted'  => false,
				'is_close'    => (bool) $status,
				'description' => $description,
				'create_time' => $time,
				'modify_time' => $time
			];
			$menuId = DB::table('menu')->insertGetId($menu);

			$menuRdd  = [
				'parent_menu_id' => intval($parent_id) ===0 ? null : $parent_id,
				'menu_id'        => $menuId,
				'sort'           => $sort == 0 ?intval($parentMenu)+1: $sort,
				'icon'           => (!empty($icon)? $icon: null),
				'create_time'    => $time,
				'modify_time'    => $time
			];
			$ret = DB::table('menu_rdd')->insert($menuRdd);

			$menuPermRdd = [
					'menu_id'       => $menuId,
					'permission_id' => $permission_id,
					'create_time'   => $time,
					'modify_time'   => $time
			];
			$ret = DB::table('menu_permission_rdd')->insert($menuPermRdd);
			$returnData['isSuccess'] = (bool) $ret;
			DB::commit();

		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception('Error'.$e);
		}
		return $returnData;
	}

	public function menuEditPost(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);

		$menuId        = $parameters['menu_id'];
		$parent_id     = $parameters['parent_id'] == 0 ? null : $parameters['parent_id'];
		$name          = $parameters['name'];
		$permission_id = $parameters['perm_id'];
		$description   = $parameters['description'];
		
		$parentMenu = $this->getMenuSort($parent_id);

		$sort          = intval($parameters['sort']) === 0 ?intval($parentMenu)+1: $parameters['sort'];
		$icon          = $parameters['icon'];
		$status        = intval($parameters['status']);
		$time = time();
		try 
		{
			DB::beginTransaction();
			$sql = 'update menu set name = ?,description = ?, is_close = ?, modify_time = ? where menu_id = ?';
			$isSuccess = DB::update($sql, [$name, $description, $status ,$time, $menuId]);

			$sql = 'update menu_rdd set parent_menu_id = ?, sort = ?, icon = ?,modify_time = ? where menu_id = ?';
			$isSuccess = DB::update($sql, [$parent_id, $sort, $icon, $time, $menuId]);

			$sql = 'update menu_permission_rdd set permission_id = ?,modify_time = ? where menu_id = ?';
			$isSuccess = DB::update($sql, [$permission_id, $time, $menuId]);

			DB::commit();

			if($isSuccess)
			{	
				//update user menu cache
				$this->updateMenuPermissionGroup($menuId);
			}
			$returnData['isSuccess'] = (bool)$isSuccess;
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}

	

	public function menuVal($parameters)
	{
		$menuId = $parameters['menuId'];
		$status = (bool) $parameters['status'];
		$flied  = 'is_close';
		return $this->changeStatus($menuId, $flied, $status);
	}

	public function menuDel($parameters)
	{
		$menuId = $parameters['menuId'];
		$status = 1;
		$flied  = 'is_deleted';
		return $this->changeStatus($menuId, $flied, $status);
	}

	public function changeStatus($menuId, $flied, $status)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> '',
			);
		$time = time();
		try 
		{
			DB::beginTransaction();
			$sql = 'update menu set '.$flied.' = ?, modify_time = ? where menu_id = ?';
			$isSuccess = DB::update($sql, [$status, $time, $menuId]);
			DB::commit();
			$returnData['isSuccess'] = (bool)$isSuccess;
			if($isSuccess)
			{	
				//update user menu cache
				$this->updateMenuPermissionGroup($menuId);
			}
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}

	public function getMenuById($menuId)
	{
		$sql = "select m.menu_id,m.name,m.description,parent_menu_id,permission_id,sort,icon,is_close from menu as m left join menu_rdd
				on m.menu_id = menu_rdd.menu_id left join menu_permission_rdd as mpr
				on m.menu_id = mpr.menu_id where m.menu_id = ?  and is_deleted = false";
		return DB::selectOne($sql,[$menuId]);
	}

	public function getMenuSort($menuId)
	{
		
//		$where = intval($menuId) === 0 ? 'menu_rdd.parent_menu_id is null': 'menu_rdd.parent_menu_id = '.$menuId;
//        $sql = 'select max(sort) as sort from menu as m left join menu_rdd
//				on m.menu_id = menu_rdd.menu_id where '.$where.' and is_close = false and is_deleted = false';
//        return DB::selectOne($sql);

        $where = intval($menuId) === 0 ? 'menu_rdd.parent_menu_id is null': 'menu_rdd.parent_menu_id = '.$menuId;
        $sql = 'select max(sort) as sort from menu as m left join menu_rdd 
				on m.menu_id = menu_rdd.menu_id where '.$where.' and is_close = false and is_deleted = false';
        $getMenuSortId = DB::selectOne($sql);

        $sortId = $where != 'menu_rdd.parent_menu_id is null' && $getMenuSortId->sort == 0 ? $this->getMenuById($menuId)->sort :$getMenuSortId->sort;
        return $sortId;
	}

	public function getParentMenu()
	{
		$sql = "select m.menu_id,m.name,parent_menu_id,menu_rdd.sort,menu_rdd.icon 
                      from menu as m 
                      left join menu_rdd on
				m.menu_id = menu_rdd.menu_id 
				where is_close = false and is_deleted = false 
				order by sort,parent_menu_id,m.menu_id";
		$menuList = DB::select($sql);
		$this->createMenuTreeHtml($menuList);
		return $this->menuTreeHtml;
	}
	
	private function createMenuTreeHtml($menuList,$parent_menu_id=0,$level=0,$html='---'){
        foreach($menuList as $menu){
            if(intval($menu->parent_menu_id) === intval($parent_menu_id)){
                if(intval($parent_menu_id) === 0){
                    $this->menuTreeHtml .= '<option value="'.$menu->menu_id.'">'.str_repeat($html,$level).$menu->name.'</option>';
                }else{
                    $this->menuTreeHtml .= '<option value="'.$menu->menu_id.'">|'.str_repeat($html,$level).$menu->name.'</option>';
                }
                $this->createMenuTreeHtml($menuList,$menu->menu_id,$level+1,$html);
            }
        }
	}

	public function updateMenuPermissionGroup($menuId)
	{
		$sql = "select ppgr.permission_group_id from menu_permission_rdd as mpr left join permission_permission_group_rdd as ppgr
		        on mpr.permission_id = ppgr.permission_id where mpr.menu_id = ?";
		$menuGroup = DB::select($sql,[$menuId]);

		foreach ($menuGroup as  $permissionGroup)
		{
			$this->userCacheService->updatePermissionCache($permissionGroup->permission_group_id);	
		}
		return true;
	}
}
