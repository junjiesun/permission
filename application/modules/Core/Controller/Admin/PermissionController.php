<?php

namespace App\Core\Controller\Admin;

use Kerisy\Http\Request;
use Kerisy\Http\Response;
use Kerisy\Log\Logger;
use Lib\Support\Paginate;
use Lib\Controller\BaseController;

use App\Core\Services\PermissionService;

class PermissionController extends BaseController
{
	private $logService;
	private $permissionService;

    public function __construct(Logger $logger,PermissionService $permissionService)
    {
        parent::__construct();
		$this->logService = $logger;
		$this->permissionService = $permissionService;
    }
	
    public function index(Request $request, Response $response)
    {
        $parameters = array();
        $permissionList = $this->permissionService->getPermissionList();
        $parameters['permissionList'] = $permissionList;
        return $response->view('permission/list', $parameters);
    }

    public function add(Request $request, Response $response)
    {
        $parameters = array();
        $parameters['type'] = 'ADD';
        $parameters['page'] = 'permission_index';
        return $response->view('permission/form',$parameters);
    }

    public function addPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['name']         = $request->input('name');
        $parameters['controller']   = $request->input('c');
        $parameters['method']       = $request->input('m');
        $parameters['type']         = $request->input('type');
        
        $returnData = $this->permissionService->permissionAddPost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function edit(Request $request, Response $response)
    {
        $permissionId = $request->input('pid');
        $parameters               = ['type' => 'EDIT'];
        $parameters['permissionInfo']   = $this->permissionService->getPermissionById($permissionId);

        if(empty($parameters['permissionInfo']))
        {
            $response->redirect('/permission');
            request()->abort = true;
            return false;
        }
        $parameters['page']       = 'permission_index';
        return $response->view('permission/form',$parameters);
    }

    public function editPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['permissionId'] = $request->input('pid');
        $parameters['name']         = $request->input('name');
        $parameters['controller']   = $request->input('c');
        $parameters['method']       = $request->input('m');
        $parameters['type']         = $request->input('type');
        
        $returnData = $this->permissionService->permissionEditPost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function editType(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['permissionId'] = $request->input('pid');
        $parameters['type']         = $request->input('type');
        
        $returnData = $this->permissionService->permissionEditType($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function delete(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $parameters = array();
        $parameters['permissionId'] = $request->input('pid');
        $returnData = $this->permissionService->permissionDel($parameters);
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
