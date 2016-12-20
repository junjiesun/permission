<?php

namespace Lib\Controller;

use Kerisy\Http\Controller;

class AppController extends Controller
{
    public function __construct()
    {
        $this->middleware = ['Lib\Middleware\Controller\Auth'];
    }

    /**
     * 必须登录才可以访问的action列表
     * @return action数组
     */
    public function guestDenyActions()
    {
        return [];
    }
    
    public function showError($error_code = 10000, $error = 'System Error')
    {
        response()->json(['error_code' => $error_code, 'error' => $error]);
        request()->abort = true;
    }
}
