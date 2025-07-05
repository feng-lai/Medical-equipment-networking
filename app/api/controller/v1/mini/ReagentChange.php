<?php

namespace app\api\controller\v1\mini;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\mini\ReagentChangeLogic;

/**
 * 试剂更换记录-控制器
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class ReagentChange extends Api
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
      'type',//
      'validity',
      'contains_t',
      'contains_ml',
      'lot_num',
      'area_code',
      'is_new',
      'change_time',
      'reserve1',
      'reserve2'
    ]);
    $this->check($request, "RegentChange.save");
    $result = ReagentChangeLogic::miniAdd($request, $this->userInfo);
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
