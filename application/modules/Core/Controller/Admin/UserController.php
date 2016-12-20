<?php

namespace App\Core\Controller\Admin;

use Kerisy\Http\Request;
use Kerisy\Http\Response;
use Kerisy\Log\Logger;
// use Lib\Support\Ptldap\Ptldap;
use Lib\Support\Paginate;
use Lib\Controller\BaseController;
use Lib\Services\UserCacheService;

use App\Core\Services\PublicService;
use App\Core\Services\UserService;

class UserController extends BaseController
{
	private $logService;
	private $userService;
    private $userCacheService;
	private $publicService;
    private $displayMenu = 'user_userlist';

    public function __construct(
		Logger $logger,
		UserService $userService,
		PublicService $publicService,
        UserCacheService $userCacheService
	)
    {
        parent::__construct();
		$this->logService = $logger;
		$this->userService = $userService;
		$this->userCacheService = $userCacheService;
        $this->publicService = $publicService;
        $this->setAllowActions(['getOrganize','getAllUser', 'userAttendanceRecords']);
    }
	
    public function userList(Request $request, Response $response)
    {
        $parameters = $search = array();
        $search['n']  = $parameters['username']  = $request->input('n','');
        $search['e']  = $parameters['email']     = $request->input('e','');
        $search['cl'] = $parameters['canLogin'] = $request->input('cl','all');
        $search = array_filter($search);

        $userInfo = $this->getUser();
        $page = $request->input('page',1);
        $parameters['limit']         = 20;
        $parameters['page']  = $page;
        $parameters['userInfo']      = $userInfo;

        //SJJ 2016.7.27 搜索时 需添加 user_id
        $parameters['user_id'] = $request->input('user_id','');

        list( $totalpage, $userList ) = $this->userService->getUserList($parameters);

        $paginate = new Paginate('/user?page={page}&'.http_build_query($search), $page, $totalpage, $parameters['limit']);
        $paginateView  = $paginate->showPages();

        $parameters['userlist']     = $userList;
        $parameters['paginateView'] = $paginateView;

        return $response->view('user/userlist', $parameters);
    }

    public function userAdd(Request $request, Response $response)
    {
        $parameters = ['page' => $this->displayMenu];
        return $response->view('user/adduser',$parameters);
    }

    public function userAddPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['username']       = $request->input('username');
        $parameters['password']       = $request->input('password');
        $parameters['email']          = $request->input('email');
        $parameters['can_login']      = $request->input('can_login');
        $parameters['type']           = $request->input('user_type');
        $parameters['addPublicGroup'] = $request->input('public_group');

        $returnData = $this->userService->addUserPost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }

        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function userVal(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $parameters = array();
        $parameters['userId'] = $request->input('uid');
        $parameters['status'] = $request->input('status');

        $returnData = $this->userService->userVal($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);

    }

    public function userDel(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $parameters = array();
        $parameters['userId'] = $request->input('uid');

        $returnData = $this->userService->userDel($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);

    }

    public function uesrPermission(Request $request, Response $response)
    {
        $userId = $request->input('uid');
        $parameters = array();

        $userInfo = $this->userService->getUserById($userId);
        if(empty($userInfo))
        {
            $response->redirect('/user');
        }
        $parameters['page']            = $this->displayMenu;
        $parameters['permissionGroup'] = $this->userService->getAllPermGrop();
        $parameters['userInfo']        = $userInfo;
        return $response->view('user/userperm', $parameters);
    }

    public function permGroupPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['userId']           = $request->input('user_id');
        $parameters['permissionGroups'] = $request->input('groups');
        
        $returnData = $this->userService->permGroupPost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }

        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function organizePost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['organize'] = $request->input('organize');
        $parameters['uid']      = $request->input('organize_uid');
        
        $returnData = $this->userService->organizePost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }

        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function getOrganize(Request $request, Response $response)
    {
        $uid = $request->input('uid');
        $organizeList = $this->userService->getUserOrganize($uid);
        $response->json($organizeList);
    }

    public function getAllUser(Request $request, Response $response)
    {
        $id       = $request->input('id','0');
        $type     = $request->input('type','');
        $ele     = $request->input('ele');
		 
        if ( $type == 'permgroup')
        {
            $allUser  = $this->userCacheService->cachePermgroupAllUser();
        }
        else
        {
            $allUser  = $this->userCacheService->cacheAllUser();
        }
        
        $typeUser = $this->userService->getUserByType($id,$type);
        $response->json(['allUser'=>$allUser, 'typeUser'=> json_encode($typeUser), 'ele' => $ele]);
    }
}
