<?php

namespace App\Core\Controller\Admin;

use Kerisy\Http\Request;
use Kerisy\Http\Response;
use Kerisy\Log\Logger;
use Lib\Support\Paginate;
use Lib\Controller\BaseController;

use App\Core\Services\MenuService;
use App\Core\Services\PermissionService;

class MenuController extends BaseController
{
	private $logService;
	private $menuService;
    private $permissionService;

    private $displayMenu = 'menu_index';
    public function __construct(Logger $logger,MenuService $menuService, PermissionService $permissionService)
    {
        parent::__construct();
        $this->logService       = $logger;
        $this->menuService      = $menuService;
        $this->permissionService = $permissionService;
    }
	
    public function index(Request $request, Response $response)
    {
        $parameters = array();
        $menuList = $this->menuService->getMenuList();
        $parameters['menulist']     = $menuList;
        $parameters['allMenu']      = $this->menuService->getAllMenu();
        return $response->view('menu/list', $parameters);
    }

    public function menuAdd(Request $request, Response $response)
    {
        $parameters                   = ['type' => 'ADD'];
        $parameters['permissionList'] = $this->permissionService->getPermissionList();
        $parameters['parentMenu']     = $this->menuService->getParentMenu();
        $parameters['page']           = $this->displayMenu;
        return $response->view('menu/form',$parameters);
    }

    public function menuAddPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['parent_id']   = $request->input('parent_menu_id');
        $parameters['name']        = $request->input('menu_name');
        $parameters['perm_id']     = $request->input('perm_id');
        $parameters['description'] = $request->input('description');
        $parameters['sort']        = $request->input('sort');
        $parameters['icon']        = $request->input('icon');
        $parameters['status']      = $request->input('status');
        
        $returnData = $this->menuService->menuAddPost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }

        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function menuEdit(Request $request, Response $response)
    {
        $menuId = $request->input('mid');
        $parameters                   = ['type' => 'EDIT'];
        $parameters['parentMenu']     = $this->menuService->getParentMenu();
        $parameters['menuInfo']       = $this->menuService->getMenuById($menuId);
        $parameters['permissionList'] = $this->permissionService->getPermissionList();

        if(empty($parameters['menuInfo']))
        {
            $response->redirect('/menu');
            request()->abort = true;
            return false;
        }
        $parameters['page']       = $this->displayMenu;
        return $response->view('menu/form',$parameters);
    }

    public function menuEditPost(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['menu_id']     = $request->input('menu_id');
        $parameters['parent_id']   = $request->input('parent_menu_id');
        $parameters['name']        = $request->input('menu_name');
        $parameters['perm_id']     = $request->input('perm_id');
        $parameters['description'] = $request->input('description');
        $parameters['sort']        = $request->input('sort');
        $parameters['icon']        = $request->input('icon');
        $parameters['status']      = $request->input('status');
        
        $returnData = $this->menuService->menuEditPost($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }

        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);
    }

    public function menuStatus(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $parameters = array();
        $parameters['menuId'] = $request->input('mid');
        $parameters['status'] = $request->input('status');

        $returnData = $this->menuService->menuVal($parameters);
        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 500;
        }
        return $response->json([
                'httpStatusCode' => $httpStatusCode,
                'message'        => $returnData['message']
        ]);

    }

    public function menuDel(Request $request, Response $response)
    {
        $httpStatusCode = 200;
        $parameters = array();
        $parameters['menuId'] = $request->input('mid');

        $returnData = $this->menuService->menuDel($parameters);
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
