<?php
namespace App\Core\Services;

use \Lib\Database\DB;
use Kerisy\Log\Logger;
use Lib\Support\User;
use Lib\Services\UserCacheService;

class PermissionService
{
	private $logService;
	private $userCacheService;

	public function __construct(Logger $logger, UserCacheService $userCacheService)
	{
		$this->logService = $logger;
		$this->userCacheService = $userCacheService;
	}

	public function getPermissionList()
	{
		
		$sql = 'select * from permission where is_deleted = false order by permission_id asc ,controller desc, type desc';
        $data = DB::select($sql);
        $permissionList = array();
        foreach ($data as $permission) 
        {
        	$controller = strtolower($permission->controller);

        	if(!isset($permissionList[$controller]))
        	{
        		$permissionList[$controller] = (Array)$permission;
        	}
    		if(!isset($permissionList[$controller]['sub']))
    		{
    			$permissionList[$controller]['sub'] = array();	
    		}
    		$permissionList[$controller]['sub'][] = (Array)$permission;
        }
        return $permissionList;
	}
	
	public function permissionAddPost(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		$name         = $parameters['name'];            
		$controller   = !empty($parameters['controller']) ? $parameters['controller'] : null;
		$method       = !empty($parameters['method']) ? $parameters['method'] : null;
		$type         = strtoupper($parameters['type']);
		$time = time();

		try 
		{
			DB::beginTransaction();
			$permission  = [
				'name'        => $name,
				'controller'  => $controller,
				'method'      => $method,
				'type'        => $type,
				'is_deleted'  => false,
				'create_time' => $time,
				'modify_time' => $time
			];
			$permissionId = DB::table('permission')->insertGetId($permission);

			$permissionGroupRdd = array(
					'permission_id'       => $permissionId,
					'permission_group_id' => ADMIN_GROUP,
					'create_time'         => $time,
					'modify_time'         => $time
				);
			
			$ret = DB::table('permission_permission_group_rdd')->insert($permissionGroupRdd);

			$returnData['isSuccess'] = (bool) $ret;
			DB::commit();

		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception('Error'.$e);
		}
		return $returnData;
	}

	public function permissionEditPost(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		$permissionId = $parameters['permissionId'];       
		$name         = $parameters['name'];            
		$controller   = !empty($parameters['controller']) ? $parameters['controller'] : null;
		$method       = !empty($parameters['method']) ? $parameters['method'] : null;
		$type         = strtoupper($parameters['type']);

		$time = time();
		try 
		{
			DB::beginTransaction();
			$sql = 'update permission set name= ?,controller = ?,method=?,type=?, modify_time = ? where permission_id = ?';
			$isSuccess = DB::update($sql, [$name, $controller,$method , $type,$time, $permissionId]);
			DB::commit();
			// $this->userCacheService->updatePermissionCache($permissionGroupId);
			$returnData['isSuccess'] = (bool)$isSuccess;
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}

	public function permissionEditType(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		$permissionId = $parameters['permissionId'];       
		$type         = strtoupper($parameters['type']);

		$time = time();
		try 
		{
			DB::beginTransaction();
			$sql = 'update permission set type=?, modify_time = ? where permission_id = ?';
			$isSuccess = DB::update($sql, [$type,$time, $permissionId]);
			DB::commit();
			$returnData['isSuccess'] = (bool)$isSuccess;
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}
	public function permissionDel($parameters)
	{
		$permissionId = $parameters['permissionId'];
		$returnData = array(
				'isSuccess' => false,
				'message'	=> '',
			);
		$time = time();
		try 
		{
			DB::beginTransaction();
			$sql = 'update permission set is_deleted = true, modify_time = ? where permission_id = ?';
			$isSuccess = DB::update($sql, [$time, $permissionId]);

			DB::commit();
			$returnData['isSuccess'] = (bool) $isSuccess;
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}


	public function getPermissionById($permissionId)
	{
		$sql = 'select * from permission where permission_id = ? and is_deleted = false';
		$permission = DB::selectOne($sql,[$permissionId]);
		return $permission; 
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
			DB::beginTransaction();
			$ret = true;
			DB::delete('delete from user_permission_group_rdd where permission_group_id = ?',[$permissionGroupId]);
			if(!empty($uids))
			{
				$userGroupRdd = array();
				foreach ($uids as $userId) {
					$userGroupRdd[] = array(
							'user_id'             => $userId,
							'permission_group_id' => $permissionGroupId,
							'create_time'         => $time,
							'modify_time'         => $time
						);
				}
				$ret = DB::table('user_permission_group_rdd')->insert($userGroupRdd);
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

	
}
