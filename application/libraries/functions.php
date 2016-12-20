<?php
/**
 *
 *
 * @author          Kaihui Wang <hpuwang@gmail.com>
 * @copyright      (c) 2015 putao.com, Inc.
 * @since           16/5/31
 */
if (!function_exists('cloud_file')) {
    function cloud_file($filename)
    {
        $url = config('config')->get('file_cloud')['read'];
        return trim($url, '/') . DIRECTORY_SEPARATOR . $filename;
    }
}

if (!function_exists('makeVerify')) {
    function makeVerify($data, $signKey)
    {
        ksort($data);
        return md5(http_build_query($data) . $signKey);
    }
}

if(!function_exists('lib_static')){
    function lib_static($filename)
    {
        return '/static/lib/' . $filename;
    }
}

if(!function_exists('admin_static')) {
    function admin_static($filename)
    {
        return '/static/admin/' . $filename;
    }
}
if(!function_exists('getRandChar')) {

    function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }
}

function encryPassword($password ,$encryPasswordSalt){
    return md5($encryPasswordSalt . md5($password));
}

if(!function_exists('validate_email')) {
    function validate_email($email)
    {
        if(preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i",$email )) {
            return true;
        }
    }
}

if (!function_exists('pageLimit')) {
    function pageLimit()
    {
        return 20;
    }
}
if (!function_exists('pageSkip')) {
    function pageSkip($page)
    {
        return ( $page - 1 ) * 20;
    }
}
if (!function_exists('formatNum')) {
    function formatNum($num)
    {
        return sprintf('%.2f',$num);
    }
}


/*统计部分 - s*/
if (!function_exists('encodeURIComponent')) {
    function encodeURIComponent($str)
    {
        $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
        return strtr(rawurlencode($str), $revert);
    }
}

if (!function_exists('decodeUnicode')) {
    function decodeUnicode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
            create_function(
                '$matches',
                'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
            ),
            $str);
    }
}

if (!function_exists('_isJson')) {
    function _isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}


function _funPathArray($arr = array()){
    $_start = '__start__';
    $data = [];
    foreach($arr as $k => $tmp)
    {
        $names = [];
        $i = 0;
        foreach($tmp as $i => $v)
        {
            if($i<=4){
                $parent_key = join($names, '_');
                if(empty($parent_key))
                {
                    $parent_key =  $_start;
                }
                $names[] = $v['path_access'];
                $tmp_key = join($names, '_');
                if(isset($data[$parent_key][$tmp_key]))
                {
                    $data[$parent_key][$tmp_key]['value']+=1;
                }
                else
                {
                    $data[$parent_key][$tmp_key]['name'] = $v['path_access'];
                    //$data[$parent_key][$tmp_key]['status'] = $v['path_status'];
                    //$data[$parent_key][$tmp_key]['time'] = $v['path_time'];
                    $data[$parent_key][$tmp_key]['value'] =1;
                    $data[$parent_key][$tmp_key]['nick'] = $v['path_access'];
                }
            }

            $i++;
        }

    }

    $list = [];
    foreach($data[$_start] as $k => $v)
    {
        $list[] = _funPathChildren($k, $data, $v);
    }

    return $list;
}
function _funPathChildren($key, $data, $value)
{
    if(!isset($data[$key]))
    {
        $value["children"] = array();
        return $value;
    }

    foreach($data[$key] as $k => $v) {
        $value["children"][] = _funPathChildren($k, $data, $v);
    }

    return $value;
}

//递归计算 - s
function _funArraySum($arr){
    $arr1 = $result = array();
    foreach ($arr as $val) {
        if(empty($val['children'])){
            $arr1[] = array($val['value']);
        }else{
            $arr1[] = array_merge(array($val['value']), _funArrayC($val['children']));
        }
    }

    //---补全某些数组不存在$val['v']，补0
    $n = 0;
    foreach ($arr1 as $v) {
        $c = count($v);
        $n = ($c>$n)?$c:$n;
    }

    foreach ($arr1 as &$v) {
        for($i=0;$i<$n;$i++){
            if(!isset($v[$i]))
                $v[$i] = 0;
        }
    }
    //---

    $result = _funArrayN($arr1);//计算多维数组单列的总值,如取第二维数组下标维0、1、2、3、4、5、6、7每列的值分别相加
    unset($arr1);

    return $result;
}
function _funArrayC($arr){
    $n =0;

    $carr = [];
    foreach ($arr as $val) {
        $n += $val['value'];
        if(!empty($val['children'])){
            $carr[] =  _funArrayC($val['children']);
        }
    }

    $reArr = _funArrayN($carr);//计算单个数组$val['v']总值

    return array_merge(array($n), $reArr);
}
function _funArrayN($carr){

    $reArr = [];
    foreach ($carr as $v) {
        foreach ($v as $k=>$v2) {
            $reArr[$k] = isset($reArr[$k])? $reArr[$k]+$v2 : $v2;
        }
    }
    return $reArr;
}
//递归计算 - e

/*根据某一特定键(下标)取出一维或多维数组的所有值；不用循环的理由是考虑大数组的效率，把数组序列化，然后根据序列化结构的特点提取需要的字符串*/
function _funGetArrayByKey(array $array, $string){
    if (!trim($string)) return false;
    preg_match_all("/\"$string\";\w{1}:(?:\d+:|)(.*?);/", serialize($array), $res);
    return $res[1];
}

function _funSameNameArray($sarr = array(), $str, $tarr = array()){
    if(!$sarr || !$str || !$tarr){ return array(); }
    $result = array();

    $tstr = implode(',', $tarr);
    $tstr = ',' . $tstr . ',';

    foreach($sarr as $val) {
        if (isset($val) && is_array($val)) {
            foreach ($val as $v) {
                $item = ',"' . $v[$str] . '",';

                if (false !== strpos($tstr, $item)) {
                    $result[$v[$str]][] = $v;
                }

            }
        }
    }

    return $result;

}

//关卡详情
function _funInArray($item, $array) {
    $str = implode(',', $array);
    $str = ',' . $str . ',';
    $item = ',' . $item .  ',';

    return false !== strpos($str, $item) ? true : false;
}
function _funSec2Time($sec){

    $sec = round($sec/60);
    if ($sec >= 60){
        $hour = floor($sec/60);
        $min = $sec%60;
        $res = $hour.':';
        $min != 0  &&  $res .= $min.':';
    }else{
        $res = $sec.':';
    }
    return $res;
}

function _funRAndF($nownum=0, $prevnum=0, $ratio=0, $type = 'r'){
    $type = in_array($type, array('r','f'))? $type: 'r';
    $diffnunm =  $nownum-$prevnum;

    $data = ($type=='f'? 'equal': '');
    if($diffnunm>0){
        $ratio = $ratio<0? $ratio*-1: $ratio;
        $data = ($type=='f'? 'asc': '+'.($ratio*100)."%");
    }elseif($diffnunm<0){
        $data = ($type=='f'? 'desc': ''.($ratio*100)."%");
    }else{
        $data = ($type=='f'? 'equal': '');
    }

    return $data;
}

if (!function_exists('isTimeFormat')) {
    function isTimeFormat($str){
        return strtotime($str) !== false;
    }
}

/*  作用由起止日期算出其中的周 - 开始时间 或 结束时间 所在的周 为其中时间且非起末时间的，去除该周
 *  @param start_date 开始日期
 *  @param end_date   结束日期
 *  @return 一个二维数组，其中一维为每周起止时间
 *  注意：end_date>state_date  2016-05-19>2016-03-02
 **/
function getWeekInfo($startdate, $enddate)
{
    $weekdata = $otweekdata = $data = array();
    //参数不能为空
    if(!empty($startdate) && !empty($enddate)){
        //先把两个日期转为时间戳
        $startdate = strtotime($startdate);
        $enddate = strtotime($enddate);
        //开始日期不能大于结束日期
        if($startdate <= $enddate){
            $end_date = strtotime("next monday", $enddate);
            if(date("w", $startdate) == 1){
                $start_date = $startdate;
            }else{
                $start_date = strtotime("last monday", $startdate);
            }

            //计算时间差多少周
            $countweek = ($end_date - $start_date) / (7*24*3600);
            for($i = 0; $i < $countweek; $i++){
                $compare_start_date = $start_date;
                $sd = date("m-d", $start_date);
                $eunix = strtotime("+ 6 days", $start_date);
                $ed = date("m-d", $eunix);
                $start_date = strtotime("+ 1 day", $eunix);

                //计算范围 - 开始时间 或 结束时间 所在的周 为其中时间且非起末时间的，去除该周
                if(($compare_start_date < $startdate && $eunix >= $startdate) || ($compare_start_date <= $enddate && $eunix > $enddate)){
                    continue;
                }
                $weekdata[$compare_start_date]['startdate'] = $compare_start_date;
                $weekdata[$compare_start_date]['enddate'] = $eunix;
                $weekdata[$compare_start_date]['str'] = implode(' ~ ', array($sd, $ed));
                for($j = 0; $j <= 6; $j++){
                    $weekdata[$compare_start_date]['list'][$j] = $compare_start_date+24*3600*$j;
                }
            }

            if($weekdata){
                $otweekdata = $weekdata;
                if(count($otweekdata) == 1){
                    $one_week = current(array_values($otweekdata));
                    $start = $one_week;
                    $end = $one_week;
                }else{
                    $start = array_shift($otweekdata);
                    $end = array_pop($otweekdata);
                    //$start = current(array_values($otweekdata))
                    //$end = end(array_values($otweekdata))
                }
                $data['start_date'] = $start['startdate'];//第一个星期的周一unix时间戳
                $data['end_start_date'] = $end['startdate'];//最后一个星期的周一unix时间戳
                $data['end_date'] = $end['enddate'];//最后一个星期的周天unix时间戳
            }
            $data['date'] = $weekdata;
            unset($weekdata);
            unset($otweekdata);
        }
    }

    return $data;
}
/*统计部分 - e*/


//根据某字段对多维数组进行排序的方法
function sortArrByField(&$array, $field, $desc = false){
    $fieldArr = array();
    foreach ($array as $k => $v) {
        $fieldArr[$k] = $v[$field];
    }
    $sort = $desc == false ? SORT_ASC : SORT_DESC;
    array_multisort($fieldArr, $sort, $array);
}