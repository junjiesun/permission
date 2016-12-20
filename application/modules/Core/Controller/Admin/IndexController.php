<?php
/**
 *  首页
 *
 * @author          Kaihui Wang <hpuwang@gmail.com>
 * @copyright      (c) 2015 putao.com, Inc.
 * @since           16/6/1
 */

namespace App\Core\Controller\Admin;

use Kerisy\Http\Request;
use Kerisy\Http\Response;
use \Lib\Controller\AdminController;
use App\Core\Model\User;
use Kerisy\Http\Controller;
use Kerisy\Log\Logger;
use Lib\Support\Paginate;
use Lib\Controller\BaseController;
use App\Core\Services\IndexService;

class IndexController extends BaseController
{
    private $logService;
    private $indexService;
    private $redis = null;

    public function __construct(Logger $logger,IndexService $indexService)
    {
        parent::__construct();
        $this->logService = $logger;
        $this->indexService = $indexService;
        $this->setNotAuthActions(['signin', 'signinPage', 'logout', 'errorPage']);
        $this->setAllowActions(['index','message','messageUpdate','messageList']);

    }

    public function index(Request $request, Response $response)
    {
//        $httpStatusCode = 200;
//        return $response->json([
//            'httpStatusCode' => $httpStatusCode,
//            'message'        => ''
//        ]);

        $parameters = array();
        $user = $this->getUser();
        $parameters['entry'] = FALSE;

        if( !empty($user['type']) && $user['type'] == 'USER' )
        {
            $parameters['entry'] = TRUE;
            // 如果启动慢 可忧化一下

            if( !empty($entryInfo) && $entryInfo->is_lock === 1 )
            {
                $parameters['entry'] = FALSE;
            }
        }
        $parameters['page'] = 'index_index';
        return $response->view('index/index', $parameters);
    }

    public function signinPage(Request $request, Response $response)
    {
        $user = $this->getUser();
        if(!empty($user))
        {
            return $response->redirect('/');
        }
        return $response->view('public/signin');
    }

    public function signin(Request $request, Response $response)
    {
        $httpStatusCode = 200;

        $parameters = array();
        $parameters['username'] = $request->input('username');
        $parameters['password'] = $request->input('password');
        $parameters['memberPass'] = $request->input('memberPass',false);

        $returnData = $this->indexService->signin($parameters);

        if ( $returnData['isSuccess'] == false )
        {
            $httpStatusCode = 403;
        }

        return $response->json([
            'httpStatusCode' => $httpStatusCode,
            'data'           => $returnData
        ]);
    }

    public function logout(Request $request, Response $response)
    {
        $user = $this->getUser();
        $userId = empty($user)?0:intval($user['userId']);
        $logout =  $this->indexService->loginout($userId);

        if($logout === true)
        {
            return $response->redirect('/signin');
        }
    }

    public function errorPage(Request $request, Response $response)
    {
        $number = $request->input('number');
        $message = '';

        switch ($number)
        {
            case 400:
                $message = 'Bad Request';
                break;
            case 401:
                $message = 'Unauthorized';
                break;
            case 403:
                $message = 'Forbidden';
                break;
            case 404:
                $message = 'Page Not Found';
                break;
            case 405:
                $message = 'Method Not Allowed';
                break;
            case 408:
                $message = 'Request Timeout';
                break;
            case 500:
                $message = 'Internal Server Error';
                break;
            case 501:
                $message = 'Not Implemented';
                break;
            case 502:
                $message = 'Bad Gateway';
                break;
            case 503:
                $message = 'Service Unavailable';
                break;
            case 504:
                $message = 'Gateway Timeout';
                break;
            case 510:
                $message = 'Not Extended';
                break;
            default:
                $message = 'An unknown state';
                break;
        }

        $parameters = array();
        $parameters['number'] = $number;
        $parameters['message'] = $message;

        return $response->view('error', $parameters);
    }




}