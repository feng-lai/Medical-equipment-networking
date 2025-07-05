<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\Device;
use app\api\model\DeviceDay;
use app\api\model\DeviceLog;
use think\Exception;
use app\api\logic\common\GeocoderLogic;
use think\Db;

/**
 * 设备统计管理-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class DeviceStatisticsLogic
{
  static public function cmsList($request,$userInfo)
  {
    $arr = [];
    for ($e=1; $e<=6; $e++) {
      $map = [];
      $request['area_uuid']?$map['area_uuid'] = $request['area_uuid']:'';
      $sim = get_sim($userInfo['uuid']);
      if($sim) $map['sim'] = ['in',$sim];
      switch ($e) {
        case 1:
          $arr[] = [
            'text'=>'设备总数',
            'type'=>$e,
            'num'=>Device::build()->where($map)->count()
          ];
          break;
        case 2:
          $map['status'] = 3;
          $arr[] = [
            'text'=>'在线设备',
            'type'=>$e,
            'num'=>Device::build()->where($map)->count()
          ];
          break;
        case 3:
          $map['status'] = 2;
          $arr[] = [
            'text'=>'活跃设备',
            'type'=>$e,
            'num'=>Device::build()->where($map)->count()
          ];
          break;
        case 4:
          $map['status'] = 1;
          $arr[] = [
            'text'=>'次活跃设备',
            'type'=>$e,
            'num'=>Device::build()->where($map)->count()
          ];
          break;
        case 5:
          $map['status'] = 4;
          $arr[] = [
            'text'=>'离线设备',
            'type'=>$e,
            'num'=>Device::build()->where($map)->count()
          ];
          break;
        case 6:
          $map['status'] = 5;
          $arr[] = [
            'text'=>'故障设备',
            'type'=>$e,
            'num'=>Device::build()->where($map)->count()
          ];
          break;
      }
    }

    return $arr;
  }
  static public function cmsDetail($request,$userInfo)
  {
    /**
    $num = intval(date('m',strtotime($request['end_time']) - strtotime($request['start_time'])));
    //月份
    $res = [];
    for ($e=1; $e<=4; $e++) {
      $arr = [];
      for ($i=0; $i<$num; $i++) {
        $month = date('Y-m',strtotime('+'.$i.' month',strtotime($request['start_time'])));
        $start = $month.'-01';
        $end =  date('Y-m-d', strtotime("$start +1 month -1 day"));
        $map['day_time'] = ['between time',[$start,$end]];

        switch ($e) {
          case 1:
            $type = '设备总数';
            $info = DeviceDay::build()->where($map)->avg('total');
            break;
          case 2:
            $type = '在线数';
            $info = DeviceDay::build()->where($map)->avg('online');
            break;
          case 3:
            $type = '离线数';
            $info = DeviceDay::build()->where($map)->avg('offline');
            break;
          case 4:
            $type = '故障数';
            $info = DeviceDay::build()->where($map)->avg('trouble');
            break;
        }

        $arr[] = [
          'date'=>$month,
          'contains'=>intval($info),
        ];
      }
      $res[] = [
        'type'=>$e,
        'text'=>$type,
        'info'=>$arr
      ];
    }
    return $res;
     * */
    $data = DeviceDay::build()->field('
      AVG(total) as total,
      AVG(online) as online,
      AVG(`offline`) as `offline`,
      AVG(`trouble`) as `trouble`,
      DATE_FORMAT(`day_time`, "%Y-%m") as day_time
      '
    )
      ->where('DATE_FORMAT(`day_time`, "%Y-%m")','between time',[$request['start_time'],$request['end_time']])
      ->group('DATE_FORMAT(`day_time`, "%Y-%m")')
      ->select();
    //$num = intval(date('m',strtotime($request['end_time']) - strtotime($request['start_time'])));
    $startDate = strtotime($request['start_time']);
    $endDate = strtotime($request['end_time']);
    $num = abs((date('Y', $endDate) - date('Y', $startDate)) * 12 + (date('m', $endDate) - date('m', $startDate)))+1;
    $res = [];
    for ($e=1; $e<=4; $e++) {
      $arr = [];
      for ($i=0; $i<$num; $i++) {
        $month = date('Y-m',strtotime('+'.$i.' month',strtotime($request['start_time'])));
        switch ($e) {
          case 1:
            $type = '设备总数';
            $info = 0;
            break;
          case 2:
            $type = '在线数';
            $info = 0;
            break;
          case 3:
            $type = '离线数';
            $info = 0;
            break;
          case 4:
            $type = '故障数';
            $info = 0;
            break;
        }
        foreach($data as $v){
          if($v->day_time == $month){
            switch ($e) {
              case 1:
                $type = '设备总数';
                $info = $v->total;
                break;
              case 2:
                $type = '在线数';
                $info = $v->online;
                break;
              case 3:
                $type = '离线数';
                $info = $v->offline;
                break;
              case 4:
                $type = '故障数';
                $info = $v->trouble;
                break;
            }
          }
        }
        $arr[] = [
          'date'=>$month,
          'contains'=>intval($info),
        ];
      }
      $res[] = [
        'type'=>$e,
        'text'=>$type,
        'info'=>$arr
      ];
    }


    return $res;
  }
}
