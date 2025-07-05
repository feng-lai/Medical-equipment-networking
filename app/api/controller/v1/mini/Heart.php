<?php

namespace app\api\controller\v1\mini;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\mini\HeartLogic;

/**
 * 心跳记录-控制器
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class Heart extends Api
{
  public $restMethodList = 'get|post|put|delete';


  public function _initialize()
  {
    parent::_initialize();

  }

  public function save()
  {
    $request = $this->selectParam([
      'sim', // SIM号
      'top_v',//上位机版本
      'btm_v',
      'install_date',
      'com_tem',
      'com_pre',
      'cal_par',
      'is_heart',//是否获取心跳
      's_number',//序列号
      'type',//设备型号
      'reserve1',
      'reserve2',
      'reserve3',
      'reserve4',
      'reserve5',//预留5
      'heart'//心跳间隔
    ]);
    $this->check($request, "Heart.save");
    if(\app\api\model\Device::build()->where('sim',$request['sim'])->count()){
      $this->validateSim();
    }

    $result = HeartLogic::miniAdd($request, $this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

  // public function update($id){
  //   $request = $this->selectParam([]);
  //   $request['uuid'] = $id;
  //   $result = UserLogic::miniEdit($request,$this->userInfo);
  //   if (isset($result['msg'])) {
  //     $this->returnmsg(400, [], [], '', '', $result['msg']);
  //   } else {
  //     $this->render(200, ['result' => $result]);
  //   }
  // }

  // public function delete($id){
  //   $result = UserLogic::miniDelete($id,$this->userInfo);
  //   if (isset($result['msg'])) {
  //     $this->returnmsg(400, [], [], '', '', $result['msg']);
  //   } else {
  //     $this->render(200, ['result' => $result]);
  //   }
  // }
}
