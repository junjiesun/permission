<?php
namespace App\Core\Services;

use Lib\Database\DB;
use Kerisy\Log\Logger;
//use App\Core\Services\OrganizationService;

class PublicService
{
	private $logService;
	private $organizationService;

	public function __construct(
		Logger $logger
//		OrganizationService $organizationService
	)
	{
		$this->logService = $logger;
	}

	public function getUserBaseInfo( int $userId )
	{
		if ( !(abs($userId) > 0) )
		{
			return null;
		}
		
		$sql = 'select u.user_id, u.name, u.email, u.head_portrait, u.type, 
					a.employee_id, ui.finger_id, ui.phone, ui.sex, ui.position_title,
					ui.position, a.rank_type, a.rank_level,
					ui.employee_type, ui.employee_status, a.entry_time as employee_date
				from user as u
					left join user_base_info_new as ui on ui.user_id = u.user_id
					left join archives_new as a on a.user_id = u.user_id
				where u.is_deleted = false and u.user_id = ?';
		
		$userInfo = DB::selectOne($sql, [$userId]);
		
		$organization = null;
		if ( count($userInfo) > 0 )
		{
			// $sql = 'select o.organization_id, o.name from user_organization_rdd as uor 
						// left join organization as o on o.organization_id = uor.organization_id
					// where uor.user_id = ?';
			// $organi = DB::selectOne($sql, [$userId]);
//			$organization = $this->organizationService->getCenterDepartment($userId);
		}
		
		// if ( !empty($organi) && !empty($organi->organization_id) )
		// {
			// $organization = $this->organizationService->getOrganization($organi->organization_id);
//			$organization[] = array(
//				'organization_id' => $organi->organization_id,
//				'name' => $organi->name
//			);

			$userInfo->organization = $organization;
		// }
		
		return $userInfo;
	}

	/**
	 * [addUserToSystemGroup 批量添加用户到系统权限组]
	 * @param Array       $userIds     [description]
	 * @param int|integer $publicGroup [默认public权限组id=2]
	 */
	public function addUserToSystemGroup(Array $userIds, int $systemGroup = 2 )
	{
		if (empty($userIds))
		{
			return null;
		}
		$userExistGroup = DB::select('select user_id from user_permission_group_rdd where user_id in ('.implode(',', $userIds).') and permission_group_id = ?',[$systemGroup]);	
		
		if(!empty($userExistGroup))
		{
			foreach ($userExistGroup as $row) 
			{
				if(($key = array_search($row->user_id, $userIds)) !== false)
				{
					unset($userIds[$key]);
				}
			}
		}
		$time = time();
		$userGroupRdd = array();
		foreach ($userIds as $userId) 
		{
			$userGroupRdd[] = array(
					'user_id'             => $userId,
					'permission_group_id' => $systemGroup,  
					'create_time'         => $time,
					'modify_time'         => $time
				);
		}
		
		$ret = DB::table('user_permission_group_rdd')->insert($userGroupRdd);
		return $ret;
	}

	/**
	 * [addUserToGroup 添加用户到权限组]
	 * @param int         $userId      [description]
	 * @param int|integer $publicGroup [description]
	 */
	public function addUserToGroup(int $userId, int $groupId = 2 )
	{
		if ( !(abs($userId) > 0) )
		{
			return null;
		}
		$group = DB::selectOne('select permission_group_id from permission_group where permission_group_id = ?',[$groupId]);
		if(empty($group)) return null;

		$userExistGroup = DB::selectOne('select user_id from user_permission_group_rdd where user_id =? and permission_group_id = ?',[$userId, $groupId]);	
		
		if(!empty($userExistGroup))
		{
			return null;
		}
		$time = time();
		$userGroupRdd = array(
			'user_id'             => $userId,
			'permission_group_id' => $groupId,  
			'create_time'         => $time,
			'modify_time'         => $time
		);
		$ret = DB::table('user_permission_group_rdd')->insert($userGroupRdd);
		return $ret;
	}

	/**
	 * 添加用户时添加员工基本信息
	 * @param array $userResults
	 * @return bool
	 */
	public function addUserToUserBaseInfo(Array $userResults = [])
	{
		$ret = false;
		if (empty($userResults['user_id']) || empty($userResults['email']))
		{
			return $ret;
		}
		$userBaseInfo = DB::selectOne("select user_base_info_id from user_base_info WHERE  job_email = '".$userResults['email']."'");
		if(empty($userBaseInfo)){
			$time = time();
			$baseInfo = array(
				'job_email' => $userResults['email'],
				'user_id' => $userResults['user_id'],
				'name' => $userResults['name'],
				'position_title' => 'USER',
				'jobemail_binding' => 2,
				'create_time' => $time,
				'modified_time' => $time
			);
			$baseInfoId = DB::table('user_base_info')->insertGetId($baseInfo);
			if($baseInfoId){
				$userEmergencyContact = array(
					'user_base_info_id' => $baseInfoId,
					'user_id' => $userResults['user_id'],
					'create_time'   => $time,
					'modified_time' => $time
				);
				$ret = DB::table('user_emergency_contact')->insert($userEmergencyContact);
			}
		}

		return (bool)$ret;
	}
}
