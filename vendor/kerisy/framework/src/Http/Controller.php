<?php
/**
 * Kerisy Framework
 *
 * PHP Version 7
 *
 * @author          Jiaqing Zou <zoujiaqing@gmail.com>
 * @copyright      (c) 2015 putao.com, Inc.
 * @package         kerisy/framework
 * @subpackage      Http
 * @since           2015/11/11
 * @version         2.0.0
 */

namespace Kerisy\Http;

use Kerisy\Core\MiddlewareTrait;

class Controller
{
    private $_user_id;
    
    use MiddlewareTrait;

    public function userId()
    {
        return $this->_user_id;
    }
    public function guestActions()
    {
        return [];
    }

    const API_SUCCESS = 200;
    const API_TIP_SUCCESS = 201;
    const API_NOTICE = 500;

    /**
     * 格式化为api数据
     *
     * @param $data 返回的数据
     * @param $code 错误编码
     * @param $msg 消息
     * @param $redirect 跳转链接
     * @return array
     */

    protected function formatApi($data, $code = self::API_SUCCESS, $redirect = "")
    {
        return ["code" => $code, "data" => $data, "redirect" => $redirect];
    }
}
