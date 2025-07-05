<?php

namespace app\api\logic\mini;

use app\common\tools\AliOss;
use think\Exception;
use think\Db;
use app\api\model\Trouble;
use app\api\model\Device;
use app\api\model\DeviceLog;
use app\api\model\Admin;
use app\api\controller\Sms;
/**
 * 故障记录-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class TroubleLogic
{


  static public function miniAdd($request)
  {
    try {
      Db::startTrans();
      //当天故障次数
      $num = Trouble::build()
        ->where('sim',$request['sim'])
        ->where('is_deleted',1)
        ->where('level','in',[1,2])
        ->where('trouble_time', 'between time',[date('Y-m-d'),date('Y-m-d').' 23:59:59'])
        ->count();

      $heart = Trouble::build();
      $heart->uuid = uuid();
      $heart->sim = $request['sim'];
      $heart->top_v = $request['top_v'];
      $heart->btm_v = $request['btm_v'];
      $heart->note = $request['note'];
      $heart->level = $request['level'];
      $heart->type = $request['type'];
      $heart->s_number = $request['s_number'];
      $heart->code = $request['code'];
      $heart->dsc = $request['dsc'];
      $heart->trouble_time = $request['trouble_time'];
      if(in_array($request['level'],[1,2])){
        $heart->is = $num+1;
      }else{
        $heart->is = 0;
      }
      $heart->save();

      //第一次故障日期
      $day = Trouble::build()->field('DATE_FORMAT(`trouble_time`, "%Y-%m-%d") as day')->where('sim',$request['sim'])->where('is',2)->where('is_deleted',1)->group('day')->order('day','asc')->find();


      if($heart->is == 2){
        //第二次报障
        Device::build()->where('sim',$request['sim'])->update(['status'=>5]);
        //记录
        DeviceLog::build()->data(['uuid'=>uuid(),'status'=>5,'sim'=>$request['sim']])->save();
      }
      //发短信
      if($day){
        $smsObj = new Sms();
        $device = Device::build()->where('sim',$request['sim'])->find();
        $arr = [
          'hospital'=>$device->hospital,
          'device_type'=>$device->type,
          'date'=>date('Y-m-d',strtotime($request['trouble_time'])),
          'time'=>date('H:i:s',strtotime($request['trouble_time'])),
          'error_code'=>$request['code'],
          'error_des'=>$request['dsc'],
          'device'=>$request['sim'],
          'name'=>$device->contact,
          'phone'=>$device->phone?$device->phone:'12345678912',
          'hos_address'=>$device->address,
        ];
        //收信人
        $admin = Admin::build()->where(['is_deleted'=>1,'disabled'=>1])->where('area_uuid','like','%'.$device->area_uuid.'%')->column('mobile');
        $admin[] = $device->mobile;
        if(date('Y-m-d',strtotime($day->day)) == date('Y-m-d',time()) && $heart->is == 2){
          foreach($admin as $v){
            //今天第一天故障发短信
            $res = $smsObj->send_notice($v,$arr);
          }

        }
        //第二天
        if(date('Y-m-d',strtotime($day->day.' +1 day')) == date('Y-m-d',time()) && $heart->is == 1){
          foreach($admin as $v){
            //今天第一天故障发短信
            $res = $smsObj->send_notice($v,$arr);
          }

        }
        //第三天
        if(date('Y-m-d',strtotime($day->day.' +2 day')) == date('Y-m-d',time()) && $heart->is == 1){
          foreach($admin as $v){
            //今天第一天故障发短信
            $res = $smsObj->send_notice($v,$arr);
          }
        }
      }
      Db::commit();
      return true;
    } catch (Exception $e) {
      Db::rollback();
      throw new Exception($e->getMessage(), 500);
    }
  }
  static public function update($request){
    try {
      if(!$request['clear_code']){
        return true;
      }
      Db::startTrans();
      Trouble::build()->where('sim',$request['sim'])->where('is_deleted',1)->update(['clear_code'=>$request['clear_code'],'is_deleted'=>2]);
      Device::build()->where('sim',$request['sim'])->update(['status'=>3]);
      //故障恢复记录
      DeviceLog::build()->data(['uuid'=>uuid(),'status'=>3,'sim'=>$request['sim']])->save();
      Db::commit();
      return true;
    } catch (Exception $e) {
      Db::rollback();
      return ['msg'=>$e->getMessage()];
    }
  }


}
