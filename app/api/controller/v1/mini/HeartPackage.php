<?php

namespace app\api\controller\v1\mini;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\mini\HeartPackageLogic;

/**
 * 心跳包-控制器
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class HeartPackage extends Api
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
    ]);
    $request['file'] = request()->file('file');
    $this->check($request, "HeartPackage.save");
    $result = HeartPackageLogic::miniAdd($request);
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
