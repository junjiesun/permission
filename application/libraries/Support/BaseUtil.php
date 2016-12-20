<?php
namespace Lib\Support;

class BaseUtil
{
    /**
     * curl请求
     * @param $url
     * @param array $data
     * @param string $method
     * @param array $header
     * @return mixed
     */
    public static function httpRequest($url, $data, $method = "post", $header = array())
    {
        if (!function_exists('curl_init')) {
            throw new \Exception("function curl_init not found", 1);
        }

        $method = strtolower($method);
        if ($method == 'get') 
        {
            $url = $url . "?" . http_build_query($data);
        }
        // Use CURL if installed...
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        if ($method == "post") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if ($method == 'put') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:43.0) Gecko/20100101 Firefox/43.0');

        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception( curl_error($ch), 1);
        }
        curl_close($ch);
        unset($ch);
        return $result;
    }

    /**
     * @param  [array] $postData
     * @return mixed
     */
    public static function sMsg( array $postData)
    {
        if(empty($postData))
        {
            return false;
        }

        $time = time();
        $authToken = env('S_AUTH_TOKEN');
        $authKey   = env('S_AUTH_KEY');
        $appid     = env('S_AUTH_APPID');
        $data = json_encode($postData, JSON_UNESCAPED_UNICODE);

        $verify    = hash_hmac('sha1', $time.$data.$authToken, $authKey);
        
        $url = env('S_MSG_URL');
        if(empty($url)) return false;

        $url .= '?verify='.$verify.'&time='.$time.'&appid='.$appid;
        $response = self::httpRequest($url, $data, 'post', array('Content-Type: text/plain'));
        $result = json_decode($response, true);

        if($result !== false)
        {
            if(isset($result['result']) && intval($result['result']) == 0)
            {
                $result = true;
            }
        }else{
            $result = $response;
        }
        SimpleLog::log('sMsg', array('url'=> $url, 'data'=> $postData, 'response'=> $response));
        return $result;
    }
}