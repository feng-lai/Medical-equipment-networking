<?php

namespace app\api\controller\v1\mini;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\mini\TroubleLogic;

/**
 * 故障记录-控制器
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class Trouble extends Api
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
      'note',
      'level',
      'trouble_time',
      'dsc',
      'code',
      'type',
      's_number'
    ]);
    $this->check($request, "Heart.save");
    $this->validateSim();
    $result = TroubleLogic::miniAdd($request, $this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }
  public function update($id){
    $request = $this->selectParam([
      'clear_code'
    ]);
    $request['sim'] = $id;
    $this->validateSim($id);
    $result = TroubleLogic::update($request);
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
