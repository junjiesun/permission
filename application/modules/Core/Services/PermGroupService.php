<?php
namespace App\Core\Services;

//use \Kerisy\Database\DB;
use \Lib\Database\DB;
use Kerisy\Log\Logger;
// use Lib\Support\Ptldap\Ptldap;
use Lib\Support\User;
use Lib\Services\UserCacheService;

class PermGroupService
{
	private $logService;
	private $indexService;

	public function __construct(Logger $logger, IndexService $indexService, UserCacheService $userCacheService)
	{
		$this->logService = $logger;
		$this->indexService = $indexService;
		$this->userCacheService = $userCacheService;
	}

	public function getPermGroupList(Array $parameters)
	{
		$page    = $parameters['page'];
		$limit   = $parameters['limit'];
		$page    = ($page > 0) ? $page : 1;
		$offset  = ($page - 1) * $limit;
		$pagetotal = 1;

		$sql = 'select permission_group_id, name, description, is_editor, is_display from permission_group where is_deleted = false';
		$pagetotal = DB::select($sql);

        if (count($pagetotal) > 0)
        {
            $pagetotal = ceil(count($pagetotal) / $limit);
        }
		
        $sql = $sql . ' limit ?, ?';

        $data = DB::select($sql, [$offset, $limit]);
        return array($pagetotal, $data);
	}

	public function getAllPermission()
	{
		$sql = "select permission_id,name,controller from permission where is_deleted = false;";
		return DB::select($sql);
	}

	public function getPermissionList()
	{
		$allPermission = $this->getAllPermission();
		$permissionList = array();

		foreach ($allPermission as  $perm) {
			$permissionList[$perm->controller][] = $perm;
		}
		return $permissionList;
	}
	public function permgroupAddPost(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		$permission  = $parameters['permission'];
		$name        = $parameters['name'];
		$description = $parameters['description'];
		try 
		{
			$time = time();
			DB::beginTransaction();
			$permissionGroup  = [
				'name'        => $name,
				'description' => $description,
				'is_deleted'  => false,
				'create_time' => $time,
				'modify_time' => $time
			];
			$permissionGroupId = DB::table('permission_group')->insertGetId($permissionGroup);

			$permissionGroupRdd = array();
			foreach ($permission as $permissionId) {
				$permissionGroupRdd[] = array(
						'permission_id'       => $permissionId,
						'permission_group_id' => $permissionGroupId,
						'create_time'         => $time,
						'modify_time'         => $time
					);
			}
			
			$ret = DB::table('permission_permission_group_rdd')->insert($permissionGroupRdd);

			$returnData['isSuccess'] = (bool) $ret;
			DB::commit();

		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception('Error'.$e);
		}
		return $returnData;
	}

	public function permGroupEditPost(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		$permission        = $parameters['permission'];       
		$name              = $parameters['name'];            
		$permissionGroupId = $parameters['permissionGroupId'];
		$description       = $parameters['description'];

		if(intval($permissionGroupId) === 1) return $returnData;
		$time = time();
		try 
		{
			DB::beginTransaction();
			$sql = 'update permission_group set name= ?,description = ?, modify_time = ? where permission_group_id = ?';
			$isSuccess = DB::update($sql, [$name, $description ,$time, $permissionGroupId]);

			DB::delete('delete from permission_permission_group_rdd where permission_group_id = ?',[$permissionGroupId]);
			
			$permissionGroupRdd = array();
			foreach ($permission as $permissionId) {
				$permissionGroupRdd[] = array(
						'permission_id'       => $permissionId,
						'permission_group_id' => $permissionGroupId,
						'create_time'         => $time,
						'modify_time'         => $time
					);
			}
			$ret = DB::table('permission_permission_group_rdd')->insert($permissionGroupRdd);
			DB::commit();

			$this->userCacheService->updatePermissionCache($permissionGroupId);
			$returnData['isSuccess'] = (bool)$ret;
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}


	public function permGroupDel($parameters)
	{
		$permissionGroupId = $parameters['permGroupId'];
		$returnData = array(
				'isSuccess' => false,
				'message'	=> '',
			);
		// if(intval($permissionGroupId) === 1) return $returnData;
		$time = time();
		try 
		{

			$sql = 'select is_editor, is_display from permission_group where permission_group_id = ?';
			$permissionGroup = DB::selectOne($sql, [$permissionGroupId]);
			
			if ( count($permissionGroup) > 0 && ( !$permissionGroup->is_editor || !$permissionGroup->is_display ) )
			{
				return array(
					'isSuccess' => false,
					'message'	=> '该权限组不能操作',
				);
			}
			
			DB::beginTransaction();

			$sql = 'update permission_group set is_deleted = true, modify_time = ? where permission_group_id = ?';
			$isSuccess = DB::update($sql, [$time, $permissionGroupId]);

			$sql = 'delete from user_permission_group_rdd where permission_group_id = ?';
			$isSuccess = DB::delete($sql, [$permissionGroupId]);

			$this->userCacheService->updatePermissionCache($permissionGroupId);
			DB::commit();
			$returnData['isSuccess'] = true;
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}


	public function getPermGroupById($permissionGroupId)
	{
		$groupPermissionInfo = array();	
		$sql = "select pg.permission_group_id, pg.name, pg.description, pgrdd.permission_id,
					pg.is_editor, pg.is_display 
				from permission_group as pg 
					left join permission_permission_group_rdd pgrdd on pg.permission_group_id = pgrdd.permission_group_id 
				where pg.permission_group_id = ? and pg.is_deleted = false";
		$groupPermission = DB::select($sql,[$permissionGroupId]);
	
		if ( count( $groupPermission ) > 0 )
		{
			if ( !$groupPermission[0]->is_editor || !$groupPermission[0]->is_display )
			{
				return $groupPermissionInfo;
			}
				
			$groupPermissionInfo['name']                = $groupPermission[0]->name;
			$groupPermissionInfo['description']         = $groupPermission[0]->description;
			$groupPermissionInfo['permission_group_id'] = $permissionGroupId;
		}
		
		foreach ($groupPermission as $perm) 
		{
			$groupPermissionInfo['permissionIds'][] = $perm->permission_id;
		}

		return $groupPermissionInfo; 
	}

	public function groupAddUser(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		$uids              = $parameters['uids'];
		$permissionGroupId = $parameters['groupId'];

		$time = time();
		try 
		{
			$sql = 'select is_editor, is_display from permission_group where permission_group_id = ?';
			$permissionGroup = DB::selectOne($sql, [$permissionGroupId]);
						
			if ( count($permissionGroup) > 0 && ( !$permissionGroup->is_editor || !$permissionGroup->is_display ) )
			{
				return array(
					'isSuccess' => false,
					'message'	=> '该权限组不能操作',
				);
			}		
				
			DB::beginTransaction();
			$ret = true;

			$sql = 'select * from user_permission_group_rdd where permission_group_id = ?';
			$userList = DB::select($sql,[$permissionGroupId]);

			$existUser = array(); //已经存在的user
			foreach ($userList as $user) 
			{
				array_push($existUser, $user->user_id);
			}
			
			$needDel = empty($uids) ? $existUser :array_diff($existUser, $uids);
			if(!empty($needDel))
			{
				DB::delete('delete from user_permission_group_rdd where permission_group_id = ? and user_id in ('.implode(',', $needDel).')',[$permissionGroupId]);
			}
			if(!empty($uids))
			{
				$needInsert = empty($existUser) ? $uids : array_diff($uids, $existUser);
				if(!empty($needInsert))
				{
					$userGroupRdd = array();
					foreach ($needInsert as $userId) {
						$userGroupRdd[] = array(
								'user_id'             => $userId,
								'permission_group_id' => $permissionGroupId,
								'create_time'         => $time,
								'modify_time'         => $time
							);
					}
					$ret = DB::table('user_permission_group_rdd')->insert($userGroupRdd);
				}
			}
			
			DB::commit();
			$returnData['isSuccess'] = (bool)$ret;

			//update user permission group to auth
			$this->userCacheService->updatePermissionCache($permissionGroupId);
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}

	//权限组用户列表
	public function getGroupUserList(Array $parameters)
	{
		$groupId = intval($parameters['groupId']);

		$page    = $parameters['page'];
		$limit   = $parameters['limit'];
		$page    = ($page > 0) ? $page : 1;
		$offset  = ($page - 1) * $limit;

		$sql = 'select u.name,u.user_id from user_permission_group_rdd as upgr left join 
			user as u on upgr.user_id = u.user_id  where upgr.permission_group_id = ? order by u.user_id';
		$pagetotal = DB::select($sql,[$groupId]);

        if (count($pagetotal) > 0) {
            $pagetotal = ceil(count($pagetotal) / $limit);
        } else {
            $pagetotal = 1;
        }
        $sql = $sql . ' limit ?, ?';

        $data = DB::select($sql, [$groupId, $offset, $limit]);
        return array($pagetotal, $data);
	}

	/**
	 * [getGroupUserByGroupId 获取用户组用户]
	 * @param  int    $groupId [description]
	 * @return [type]          [description]
	 */
	public function getGroupUserByGroupId(int $groupId)
	{
		if(!(abs($groupId) > 0)) return null;

		$sql = 'select u.name,u.user_id from user_permission_group_rdd as upgr left join 
			user as u on upgr.user_id = u.user_id  where upgr.permission_group_id = ? order by u.user_id';
		return DB::select($sql,[$groupId]);
	}

	//获取权限组可用权限
	public function getGroupPermission(int $groupId)
	{
		$sql = 'select p.* from permission_group as pg left join permission_permission_group_rdd as ppgr
			on pg.permission_group_id = ppgr.permission_group_id left join permission as p
			on ppgr.permission_id = p.permission_id where p.is_deleted = false and pg.permission_group_id = ? order by p.permission_id asc ,controller desc, type desc';
        $data = DB::select($sql,[$groupId]);
        $permissionList = array();
        foreach ($data as $permission) 
        {
        	if(!isset($permissionList[$permission->controller]))
        	{
        		$permissionList[$permission->controller] = (Array)$permission;
        	}
    		if(!isset($permissionList[$permission->controller]['sub']))
    		{
    			$permissionList[$permission->controller]['sub'] = array();	
    		}
    		$permissionList[$permission->controller]['sub'][] = (Array)$permission;
        }
        return $permissionList;
	}

	//添加用户在权限组内可用权限
	public function addUserGroupPermission(int $groupId, $permission, int $userId)
	{
		if (!(abs($groupId) > 0) || !(abs($userId) > 0) )
		{
			return false;
		}

		$time = time();
		try 
		{
			DB::beginTransaction();

			DB::delete('delete from permission_group_user_permission_rdd where user_id =? and permission_group_id = ?',[$userId, $groupId]);

			if(!empty($permission))
			{
				foreach ($permission as $permissionId) 
				{
					$permissionRdd[] = array(
							'user_id'             => $userId,
							'permission_group_id' => $groupId,
							'permission_id'       => $permissionId,
							'create_time'         => $time,
							'modify_time'         => $time
						);
				}
				DB::table('permission_group_user_permission_rdd')->insert($permissionRdd);
			}
			DB::commit();

			//update user permission group to auth
			$this->userCacheService->setUserCache($userId);
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return true;
	}

	public function getUserGroupPermission($userId, $groupId)
	{
		$sql = 'select permission_id from permission_group_user_permission_rdd where user_id = ? and permission_group_id = ?';
		$data = DB::select($sql,[$userId, $groupId]);
		$list = array();
		foreach ($data as $permission) 
		{
			array_push($list, $permission->permission_id);
		}

		return $list;
	}

	public function saveUserPermInGroup(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);

		$groupId    = $parameters['gid'];
		$userId     = $parameters['uid'];
		$permission = $parameters['permission'];

		$ret = $this->addUserGroupPermission($groupId, $permission, $userId);
		$returnData['isSuccess'] = $ret;
		if($ret == false)
		{
			$returnData['message'] = '保存失败';
		}
		return $returnData;
	}
	
	public function permissionUserDel(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);

		$groupId = intval($parameters['gid']);
		$userId  = intval($parameters['uid']);

		$time = time();
		try 
		{
			DB::beginTransaction();

			DB::delete('delete from user_permission_group_rdd where user_id =? and permission_group_id = ?',[$userId, $groupId]);
			DB::delete('delete from permission_group_user_permission_rdd where user_id =? and permission_group_id = ?',[$userId, $groupId]);
			DB::commit();
			$returnData['isSuccess'] = true;
			//update user permission group to auth
			$this->userCacheService->setUserCache($userId);
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}

	/**
	 * 检查用户是否在某个权限组
	 */
	public function checkUserInGroup(int $userId, int $groupId)
	{
		if(!(abs($userId) > 0) || !(abs($groupId)>0)) return false;

		$sql = 'select count(user_id) as total from user_permission_group_rdd where user_id = ? and permission_group_id = ?';
		$row = DB::selectOne($sql,[$userId, $groupId]);
		return empty($row) || intval($row->total) ==0 ? false : true;
	}
}
