<?php
namespace App\Core\Services;

use Lib\Database\DB;
use Kerisy\Log\Logger;
use Lib\Services\UserCacheService;
use Lib\Support\BaseUtil;

class IndexService
{
	private $logService;
    private $userCacheService;

	public function __construct(
                Logger $logger, 
                UserCacheService $userCacheService,
                PublicService $publicService
        )
	{
		$this->logService = $logger;
        $this->userCacheService = $userCacheService;
        $this->publicService = $publicService;
	}

	public function signin(Array $parameters)
	{
		$returnData = array(
			'isSuccess' => false,
			'message'   => '用户名或密码错误',
			'url'       => '/'
		);
        $memberPass = isset($parameters['memberPass']) ? (bool) $parameters['memberPass'] : false;

		$loginRet = $this->checkSysLogin($parameters);

		if($loginRet['isSuccess'] === true)
		{
            $returnData['message']  = 'success';
			$this->setUserSession($loginRet['user'], $memberPass);
			$returnData['isSuccess'] = true;
		}
		return $returnData;
	}

	public function setUserSession( $user = array() , $memberPass = false)
	{
		$sessionId = sha1(json_encode($user) . time());
		
		$position = null;
		if($user->type == 'USER')
		{
			$baseInfo  = $this->getUserBaseInfo($user->user_id);
			if ( !empty($baseInfo) )
			{
				$position = $baseInfo->position;
			}
		}
		
		$userInfo = array(
			'userId'        => $user->user_id,
			'name'          => $user->name,
			'email'         => $user->email,
			'position'		=> $position,
			'head_portrait' => isset($user->head_portrait) ? $user->head_portrait : '/images/user.png' ,
			'type'			=> $user->type
		);

        $this->userCacheService->permissionCache($user);
        $this->userCacheService->menuCache($user);

        $exp = $memberPass ? time()+3600*24*30 : 0;
		//设置有效时间的方式
		session()->storage->timeout($exp);
        session()->set($sessionId, $userInfo, $exp);
        response()->setCookie('sid', $sessionId, $exp, '/', '');
		return true;
	}

	public function getUserBaseInfo(int $userId)
	{
		$sql = 'select position from user_base_info_new where user_id = ?';
		$baseInfo = DB::selectOne($sql,[$userId]);
		return $baseInfo;
	}

	public function checkSysLogin(Array $parameters)
	{
		$username = $parameters['username'];
		$password = sha1($parameters['password']);

		$returnData = array(
			'isSuccess'  => false,
			'user'       => []
		);

		$sql = "select user_id,head_portrait,name,email,type from user where email = ? and password = ? and type != 'USER' and can_login = 1 and is_deleted = 0";
		$user = DB::selectOne($sql,[$username, $password]);

		if(!empty($user))
		{
			$returnData['isSuccess'] = true;
			$returnData['user']      = $user;
		}

		return $returnData;
	}

	private function createNonceStr($length = 6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    
	public function getUserByMail($val, $flied='email')
	{
		$sql = 'select name,email,head_portrait,type,can_login,is_deleted from user where '.$flied.' = ? and type != "USER"';
		$user = DB::select($sql, [$val]);
		return $user;
	}

	public function loginout(int $userId)
    {
    	$user = DB::selectOne("select user_id,name from user where user_id = ?",[$userId]);
    	if(!empty($user))
    	{
    		$this->userCacheService->clearUserCache($user);	
    	}
        $cookies = request()->cookie;

        if ( array_key_exists("sid", $cookies ) )
        {
            $key = $cookies['sid'];
            session()->destroy($key);
            response()->setCookie( 'sid', '', time() - 3600 );

            if( !empty( session()->get($key) ) )
            {
                return false;
            }
        }
        
        return true;
    }
}
