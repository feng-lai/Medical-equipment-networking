<?php

namespace app\api\logic\mini;

use app\api\logic\common\GeocoderLogic;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;
use app\api\model\Heart;
use app\api\model\Device;
/**
 * 心跳记录-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class HeartLogic
{
  static public function miniAdd($request)
  {
    try {
      $request['install_date'] = $request['install_date']?$request['install_date']:now_time(time());
      DB::startTrans();
      //是否存在sim号的设备
      $device = Device::build()->where('sim',$request['sim'])->find();
      if(!$device){
        $request['heart'] = $request['heart']?$request['heart']:1;
        //新增
        $device = Device::build();
        $device->uuid = uuid();
        $device->heart = $request['heart'];
        $device->is_heart = $request['is_heart'];
      }else{

        //更新
        if($device->heart){
          $request['heart'] = $device->heart;
        }else{
          $device->heart = $request['heart'];
        }
        //是否请求心跳后台数据为准
        $request['is_heart'] = $device->is_heart;
      }
      $device->sim = $request['sim'];
      $device->type = $request['type'];
      $device->reserve1 = $request['reserve1'];
      $device->reserve2 = $request['reserve2'];
      $device->reserve3 = $request['reserve3'];
      $device->reserve4 = $request['reserve4'];
      $device->reserve5 = $request['reserve5'];
      $device->install_date = $request['install_date'];
      $device->heart_time = now_time(time());
      $device->s_number = $request['s_number'];
      $device->top_v = $request['top_v'];
      $device->btm_v = $request['btm_v'];
      if($request['reserve1']){
        $device->address = $request['reserve1'];
        //经纬度
        //获取经纬度
        $result = GeocoderLogic::address($request['reserve1']);
        if(!isset($result['msg'])){
          $device->lgt = $result[0];
          $device->lat = $result[1];
        }
        $device->hospital = $request['reserve2'];
        $device->contact = $request['reserve3'];
        $device->phone = $request['reserve4'];
        $device->s_number = $request['reserve5'];
      }
      $device->save();

      //心跳记录
      //当天是否存在心跳
      $heart = Heart::build()->whereTime('create_time',date('Y-m-d'))->where('sim',$request['sim'])->find();
      if(!$heart){
        $heart = Heart::build();
        $heart->uuid = uuid();
      }
      $heart->heart = $request['heart'];
      $heart->is_heart = $request['is_heart'];
      $heart->sim = $request['sim'];
      $heart->s_number = $request['s_number'];
      $heart->top_v = $request['top_v'];
      $heart->btm_v = $request['btm_v'];
      $heart->com_tem = $request['com_tem'];
      $heart->com_pre = $request['com_pre'];
      $heart->cal_par = $request['cal_par'];
      $heart->reserve1 = $request['reserve1'];
      $heart->reserve2 = $request['reserve2'];
      $heart->reserve3 = $request['reserve3'];
      $heart->reserve4 = $request['reserve4'];
      $heart->reserve5 = $request['reserve5'];
      $heart->install_date = $request['install_date'];
      $heart->is_heart = $request['is_heart'];
      $heart->create_time = now_time(time());;
      $heart->update_time = now_time(time());
      $heart->save();
      DB::commit();
      Device::build()->where('sim',$request['sim'])->update(['is_heart'=>0]);
      return $request;
    } catch (Exception $e) {
      DB::rollback();
      throw new Exception($e->getMessage(), 500);
    }
  }


}
