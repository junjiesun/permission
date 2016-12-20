<?php

namespace App\Core\Controller\Admin;

use Kerisy\Http\Request;
use Kerisy\Http\Response;
use Kerisy\Log\Logger;
use Lib\Support\Paginate;
use Lib\Controller\BaseController;

use App\Core\Services\PermGroupService;
use App\Core\Services\PermissionService;

class PermGroupController extends BaseController
{
	private $logService;
	private $permGroupService;
    private $permissionService;
    private $displayMenu = 'permgroup_index';

    public function __construct(
        Logger $logger,
        PermGroupService $permGroupService,
        PermissionService $permissionService)
    {
        parent::__construct();
		$this->logService = $logger;
		$this->permGroupService = $permGroupService;
        $this->permissionService = $permissionService;
    }
	
    public function index(Request $request, Response $response)
    {
        $page = $request->input('page', 1);
        $parameters['limit']         = 20;
        $parameters['page']          = $page;

        list( $totalpage, $permGroupList ) = $this->permGroupService->getPermGroupList($parameters);

        $paginate = new Paginate('/permgroup?page={page}', $page, $totalpage, $parameters['limit']);
        $paginateView  = $paginate->showPages();

        $parameters['permGrouplist'] = $permGroupList;
        $parameters['paginateView']  = $paginateView;

        return $response->view('permgroup/list', $parameters);
    }

    public function permGroupAdd(Request $request, Response $response)
    {
        $parameters               = ['type' => 'ADD'];
        $parameters['permissionList'] = $this->permissionService->getPermissionList();
        $parameters['page']       = $this->displayMenu;
        return $response->view('permgroup/form',$parameters);
    }

    public function permGroupAddPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['permission']  = $request->input('permission');
        $parameters['name']        = $request->input('name');
        $parameters['description'] = $request->input('description');
        
        $returnData = $this->permGroupService->permgroupAddPost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }

        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function permgroupEdit(Request $request, Response $response)
    {
        $permGroupId = $request->input('permgroupid');
        $parameters               = ['type' => 'EDIT'];
        $parameters['permissionList'] = $this->permissionService->getPermissionList();
        $parameters['permGroupInfo']   = $this->permGroupService->getPermGroupById($permGroupId);

        if(empty($parameters['permGroupInfo']))
        {
            $response->redirect('/permgroup');
            request()->abort = true;
            return false;
        }
        $parameters['page']       = $this->displayMenu;
        return $response->view('permgroup/form',$parameters);
    }

    public function permGroupEditPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['permission']        = $request->input('permission');
        $parameters['name']              = $request->input('name');
        $parameters['permissionGroupId'] = $request->input('perm_group_id');
        $parameters['description']       = $request->input('description');
        $parameters['user']              = $this->getUser();
        
        $returnData = $this->permGroupService->permGroupEditPost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function permGroupDel(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $parameters = array();
        $parameters['permGroupId'] = $request->input('perm_group_id');
        $returnData = $this->permGroupService->permGroupDel($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function addUserPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $parameters = array();
        $parameters['uids']    = $request->input('uids');
        $parameters['groupId'] = $request->input('group_id');
        $returnData = $this->permGroupService->groupAddUser($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function userList(Request $request, Response $response)
    {
        $parameters = array();
        $parameters['groupId'] = $request->input('gid');

        $page = $request->input('page',1);
        $parameters['limit']         = 20;
        $parameters['page']          = $page;

        $parameters['permGroupInfo'] = $this->permGroupService->getPermGroupById($parameters['groupId']);
        if(empty($parameters['permGroupInfo']))
        {
            $response->redirect('/permgroup');
            request()->abort = true;
            return false;
        }

        list( $totalpage, $groupUserList ) = $this->permGroupService->getGroupUserList($parameters);

        $parameters['groupPermission'] = $this->permGroupService->getGroupPermission($parameters['groupId']);

        $paginate = new Paginate('/permgroup/user?gid='.$parameters['groupId'].'&page={page}', $page, $totalpage);
        $paginateView  = $paginate->showPages();

        $parameters['groupUserList'] = $groupUserList;
        $parameters['paginateView']  = $paginateView;
        $parameters['page']       = $this->displayMenu;
        return $response->view('permgroup/userlist', $parameters);
    }

    //获取用户在权限组里所拥有的权限
    public function getUserGroupPermission(Request $request, Response $response)
    {
        $groupId = $request->input('gid');
        $userId     = $request->input('uid');

        $list =  $this->permGroupService->getUserGroupPermission($userId, $groupId);

        return $response->json($list);
    }

    public function permUserEditPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $parameters = array();
        $parameters['uid']        = $request->input('uid');
        $parameters['gid']        = $request->input('gid');
        $parameters['permission'] = $request->input('permission');

        $returnData = $this->permGroupService->saveUserPermInGroup($parameters);
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
        $parameters['uid']        = $request->input('uid');
        $parameters['gid']        = $request->input('gid');

        $returnData = $this->permGroupService->permissionUserDel($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }
}
