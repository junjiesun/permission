<?php
/**
 * Created by PhpStorm.
 * User: IceQi
 * Date: 2016/2/24
 * Time: 19:51
 */

namespace App\Core\Controller\Front;
//use ElasticSearch\Client;
use Kerisy\Http\Request;
use Kerisy\Http\Response;

use App\Core\Model\Module;
use App\Core\Model\Role;
use App\Core\Model\User;
use App\Core\Model\UserRole;
use Lib\Controller\AdminController;
use App\Common\Helper\Pageination as PageinationHelper;

use Kerisy\Http\Controller;

class PostActiveRecordController extends Controller
{
    public function index(Request $request , Response $response)
    {
        $httpStatusCode = 200;
        return $response->json([
            'httpStatusCode' => $httpStatusCode,
            'message'        => ''
        ]);
    }
}