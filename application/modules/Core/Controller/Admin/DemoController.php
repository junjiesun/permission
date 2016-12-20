<?php

namespace App\Core\Controller\Admin;

use Kerisy\Http\Request;
use Kerisy\Http\Response;
use Lib\Controller\BaseController;

class DemoController extends BaseController
{
    public function index(Request $request, Response $response)
    {
        $parameters['page'] = 'demo_index';
        return $response->view('demo/index', $parameters);
    }
}
