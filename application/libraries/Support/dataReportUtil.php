<?php
namespace Lib\Support;

class dataReportUtil
{
    
    public static function getAge(int $birthday)
    {
      list($y,$m,$d) = explode("-",date("Y-m-d",$birthday));

      $now = strtotime("now");
      list($ny,$nm,$nd) = explode("-",date("Y-m-d",$now)); 
      $age = $ny - $y; 
      if((int)($nm.$nd) < (int)($m.$d)) 
        $age -= 1; 
      return $age; 
    }

    public static function getMonthNum(int $start, int $end)
    {
        list($s['y'],$s['m'])=explode("-",date('Y-m',$start));
        list($e['y'],$e['m'])=explode("-",date('Y-m',$end));
        return abs($s['y']-$e['y'])*12 + $e['m']-$s['m'];
    }
}