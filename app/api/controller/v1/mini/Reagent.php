<?php

namespace app\api\controller\v1\mini;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\mini\ReagentLogic;

/**
 * 试剂余量-控制器
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class Reagent extends Api
{
  public $restMethodList = 'post';


  public function _initialize()
  {
    parent::_initialize();
    $this->validateSim();
  }

  public function save()
  {
    $request = $this->selectParam([
      'sim', // SIM号
      'reagent_a',
      'reagent_b',
      'reagent_c',
      'reagent_h',
      'chr_clo',
      'reserve1',
      'reserve2',
      'reserve3',
      'reserve4',
      'reserve5',
      'sum',
      'res_val',
      'type',
      'test_time',
      'blood_sugar',
      'lactate',
      'number',
      'origin',
      'par2',
      'area'
    ]);
    $this->check($request, "Heart.save");
    $result = ReagentLogic::miniAdd($request, $this->userInfo);
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
