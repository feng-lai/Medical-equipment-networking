<?php

namespace app\api\controller\v1\mini;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\mini\CheckCalibrationLogic;

/**
 * 质检-校准-控制器
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class CheckCalibration extends Api
{
  public $restMethodList = 'get|post|put|delete';


  public function _initialize()
  {
    parent::_initialize();
    $this->validateSim();
  }

  public function save()
  {
    $request = $this->selectParam([
      'sim', // SIM号
      'kind',//上位机版本
      'type',
      'test_time',
      'b_number',
      'ca_val',
      'ctr_val',
      'ca_res',
      'ca_t_cv',
      'ca_b_cv',
      'qc_res_b',
      'qc_res_t',
      'qc_b_sd',
      'qc_t_sd',
      'qc_b_cv',
      'qc_t_cv',
      'origin',
      'new_cal',
      'cal',
      'reserve3'
    ]);
    $this->check($request, "CheckCalibration.save");
    $result = CheckCalibrationLogic::miniAdd($request, $this->userInfo);
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
