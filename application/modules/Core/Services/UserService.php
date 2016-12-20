<?php
namespace App\Core\Services;

use  Lib\Database\DB;
use Kerisy\Log\Logger;
use Lib\Support\User;
use Lib\Services\UserCacheService;

class UserService
{
	private $logService;
	private $indexService;
	private $userCacheService;
	private $publicService;

	public function __construct(
			Logger $logger, 
			IndexService $indexService,
			UserCacheService $userCacheService,
			PublicService $publicService)
	{
		$this->logService   = $logger;
		$this->indexService = $indexService;
		$this->userCacheService = $userCacheService;
		$this->publicService = $publicService;
	}

	public function getUserList(Array $parameters)
	{
		$userInfo = $parameters['userInfo'];
        $user_id  = $parameters['user_id'];
		$username = $parameters['username'];
		$email    = $parameters['email'];
		$canLogin = $parameters['canLogin'];

		$where = ' and `type` != "SUPER_ADMIN"';

		if($userInfo['type'] !== 'SUPER_ADMIN') $where .= 'and `type` = "USER"';
		if(!empty($username)) $where .= ' and `name` like "%'.$username.'%"';
		if(!empty($email))  $where .= ' and `email` like "%'.$email.'%"';
        if(!empty($user_id)) $where .= ' and user_id = '.$user_id;
		if($canLogin != 'all') $where .= ' and `can_login` = '.$canLogin;

		$page    = $parameters['page'];
		$limit   = $parameters['limit'];
		$page    = ($page > 0) ? $page : 1;
		$offset  = ($page - 1) * $limit;

		$sql = 'select `user_id`,`name`,`email`,`head_portrait`,`type`,`can_login`,`is_deleted` from `user` 
				where `is_deleted` = 0 '. $where. ' order by user_id';
		$pagetotal = DB::select($sql);

        if (count($pagetotal) > 0) {
            $pagetotal = ceil(count($pagetotal) / $limit);
        } else {
            $pagetotal = 1;
        }
        $sql = $sql . ' limit ?, ?';

        $data = DB::select($sql, [$offset, $limit]);
        return array($pagetotal, $data);
	}

	public function getUserById($userId)
	{
		$sql = 'select user.`user_id`,`name`,`email`,`permission_group_id` from `user` left join user_permission_group_rdd as upgr on user.user_id = upgr.user_id
				where `is_deleted` = false  and user.user_id = ?';
		$userPermGroup = DB::select($sql,[$userId]);

		$userGroupPermissionInfo = array();
		if(!empty($userPermGroup))
		{
			$userGroupPermissionInfo['name']    = $userPermGroup[0]->name;
			$userGroupPermissionInfo['user_id'] = $userId;

			foreach ($userPermGroup as $perm) 
			{
				$userGroupPermissionInfo['permissionGIds'][] = $perm->permission_group_id;
			}
		}
		
		return $userGroupPermissionInfo;
	}

	public function getAllPermGrop()
	{
		$sql = 'select permission_group_id,name from permission_group where is_deleted = false AND name !="SUPERADMIN"';
		$allPermGroup = DB::select($sql);
		return $allPermGroup;
	}

	public function permGroupPost(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		$userId           = $parameters['userId'];       
		$permissionGroups = $parameters['permissionGroups'];
		$time = time();
		try 
		{
			DB::beginTransaction();
			$ret = true;
			DB::delete('delete from user_permission_group_rdd where user_id = ?',[$userId]);

			if(!empty($permissionGroups))
			{
				$userGroupRdd = array();
				foreach ($permissionGroups as $groupId) {
					$userGroupRdd[] = array(
							'user_id'             => $userId,
							'permission_group_id' => $groupId,
							'create_time'         => $time,
							'modify_time'         => $time
						);
				}
				$ret = DB::table('user_permission_group_rdd')->insert($userGroupRdd);
			}
			DB::commit();
			$returnData['isSuccess'] = (bool)$ret;

			//update user permission group to auth
			$this->userCacheService->setUserCache($userId);

		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}

	public function addUserPost(Array $parameters)
	{
		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);

		$username       = $parameters['username'];
		$email          = $parameters['email'];
		$password       = $parameters['password'];
		$canLogin       = $parameters['can_login'];
		$type           = $parameters['type'];
		$addPublicGroup = $parameters['addPublicGroup'];

		if(User::checkEmail($email) === false)
		{
			$returnData['message'] = '邮箱地址不正确';
			return $returnData;
		}

		if(User::checkPwd($password) === false)
		{
			$returnData['message'] = '密码格式不正确';
			return $returnData;
		}

		$user = $this->getUserByMail($email, $type);
		if($user)
		{
			$returnData['message'] = '该用户已存在';
			return $returnData;
		}

		try 
		{
			$time = time();
			DB::beginTransaction();
			
			$user  = [
				'name'             => $username,
				'email'            => $email,
				'password'         => sha1($password),
				// 'head_portrait' => '',
				'type'             => $type,
				'can_login'        => (bool)$canLogin,
				'is_deleted'       => false,
				'is_ldap_user'     => false,
				'create_time'      => $time,
				'modified_time'    => $time
			];
			if($type == 'ADMIN')
			{
				$maxUserId = DB::selectOne('select max(user_id) as uid from user where type !="USER"');
				$user['user_id'] = intval($maxUserId->uid)+1;
			}

			$userId = DB::table('user')->insertGetId($user);
			if((bool) $addPublicGroup == true && intval($userId) > 0)
			{
				$this->publicService->addUserToGroup($userId,PUBLIC_GROUP);
			}

			$user['user_id'] = $userId;
			$this->userCacheService->setCacheAllUserByType();
			$this->userCacheService->setCachePermgroupAllUser();
			DB::commit();

		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception('Error'.$e);
		}

		if(intval($userId) > 0)
		{
			$returnData['isSuccess'] = true;
			$returnData['userId']    = $userId;
		}
		return $returnData;
	}

	public function userVal($parameters)
	{
		$userID = $parameters['userId'];
		$status = (bool)$parameters['status'];

		$returnData = array(
				'isSuccess' => false,
				'message'	=> '',
			);
		$time = time();
		try 
		{
			DB::beginTransaction();
			$sql = 'update user set can_login = ?, modified_time = ? where user_id = ?';
			$isSuccess = DB::update($sql, [$status, $time, $userID]);
			DB::commit();
			$returnData['isSuccess'] = (bool)$isSuccess;
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}

	public function userDel($parameters)
	{
		$userID = $parameters['userId'];
		$returnData = array(
				'isSuccess' => false,
				'message'	=> '',
			);
		try 
		{
			$time = time();
			DB::beginTransaction();
			$sql = 'update user set is_deleted = ?, modified_time = ? where user_id = ?';
			$isSuccess = DB::update($sql, [1, $time, $userID]);
			DB::commit();
			$returnData['isSuccess'] = (bool)$isSuccess;
		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception("Error ".$e);
		}
		return $returnData;
	}

	public function getUserByMail($val, $type, $flied='email')
	{
		$sql = 'select name,email,head_portrait,type,can_login,is_deleted from user where '.$flied.' = ? and type = ?';
		$user = DB::select($sql, [$val, $type]);
		return $user;
	}

	public function organizePost($parameters)
	{
		$uid          = $parameters['uid'];
		$organizeData = $parameters['organize'];

		$returnData = array(
				'isSuccess' => false,
				'message'	=> ''
			);
		try 
		{
			$time = time();
			DB::beginTransaction();
			$ret = true;
			DB::delete("delete from user_organization_rdd where user_id = ?",[$uid]);

			if(!empty($organizeData))
			{
				$userOrganizeRdd = array();
				foreach ($organizeData as $organizeId) {
					$userOrganizeRdd[] = array(
							'user_id'         => $uid,
							'organization_id' => $organizeId,
							'create_time'     => $time,
							'modify_time'     => $time
						);
				}
				$ret = DB::table('user_organization_rdd')->insert($userOrganizeRdd);
			}
			
			$returnData['isSuccess'] = (bool) $ret;
			DB::commit();

		} catch (\Exception $e) {
			DB::rollback();
			throw new \Exception('Error'.$e);
		}
		return $returnData;
	}

	public function getUserOrganize($uid)
	{
		$sql = "select organization_id from user_organization_rdd where user_id = ?";
		return DB::select($sql,[$uid]);
	}

	public function getUserByType($id, $type)
	{
		$users = array();
		$where = '';
		switch ($type) {
			case 'permgroup':
					$rddTable = 'user_permission_group_rdd';
					$whereId  = 'permission_group_id';
				break;
			case 'organize':
					$rddTable = 'user_organization_rdd';
					$whereId  = 'organization_id';
				break;
			case 'duties':
					$rddTable = 'duties_user_rdd';
					$whereId  = 'duties_id';
					$where = ' and rdd.is_deleted = false';
				break;
			default:
					return $users;
				break;
		}
		$sql = 'select u.user_id from '.$rddTable.' as rdd left join  `user` as u on rdd.user_id = u.user_id
				where u.is_deleted = false and u.can_login = true and rdd.'.$whereId.' = ?'.$where;
		$userList =  DB::select($sql,[$id]);
		foreach ($userList as $user) 
		{
			array_push($users, $user->user_id);
		}
		return $users;
	}
}
