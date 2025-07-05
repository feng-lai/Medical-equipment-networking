<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\ReagentChange;
use think\Exception;
use think\Db;

/**
 * 试剂更换记录统计-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ReagentChangeStatisticsLogic
{
  static public function cmsList($request,$userInfo)
  {
    if($request['type'] == 1){
      //$num = intval(date('m',strtotime($request['end_time']) - strtotime($request['start_time'])));
      $num = diffMonth($request['start_time'],$request['end_time'])+1;
      //月份
      $res = [];
      for ($e=1; $e<=4; $e++) {
        switch ($e) {
          case 1:
            $type = '洗脱液A';
          break;
          case 2:
            $type = '洗脱液B';
            break;
          case 3:
            $type = '洗脱液C';
            break;
          case 4:
            $type = '溶血剂H';
            break;
        }
        $arr = [];
        for ($i=0; $i<$num; $i++) {
          $month = date('Y-m',strtotime('+'.$i.' month',strtotime($request['start_time'])));
          $start = $month.'-01';
          $end =  date('Y-m-d', strtotime("$start +1 month -1 day"));
          $map['change_time'] = ['between time',[$start,$end.' 23:59:59']];
          $map['type'] = $e;
          $request['sim']?$map['sim'] = $request['sim']:'';
          $info = ReagentChange::build()->where($map)->sum('contains_t');
          $arr[] = [
            'date'=>$month,
            'contains'=>$info,
          ];
        }
        $res[] = [
          'type'=>$e,
          'text'=>$type,
          'info'=>$arr
        ];
      }
    }else{
      //年份
      $num = intval($request['end_time'] - $request['start_time']);
      $res = [];
      for ($e=1; $e<=4; $e++) {
        switch ($e) {
          case 1:
            $type = '洗脱液A';
            break;
          case 2:
            $type = '洗脱液B';
            break;
          case 3:
            $type = '洗脱液C';
            break;
          case 4:
            $type = '溶血剂H';
            break;
        }
        $arr = [];
        for ($i=0; $i<=$num; $i++) {
          $year = $request['start_time']+$i;
          $map['change_time'] = ['between time',[$year.'-01-01',$year.'-12-31 23:59:59']];
          $map['type'] = $e;
          $request['sim']?$map['sim'] = $request['sim']:'';
          $info = ReagentChange::build()->where($map)->sum('contains_t');
          $arr[] = [
            'date'=>$year,
            'contains'=>$info,
          ];
        }
        $res[] = [
          'type'=>$e,
          'text'=>$type,
          'info'=>$arr
        ];
      }
    }
    return $res;
  }





}
