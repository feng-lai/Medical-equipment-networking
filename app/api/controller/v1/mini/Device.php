<?php

namespace app\api\controller\v1\mini;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\mini\DeviceLogic;

/**
 * 设备-控制器
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class Device extends Api
{
  public $restMethodList = 'get|post';


  public function _initialize()
  {
    parent::_initialize();
  }

  public function index()
  {
    $request = $this->selectParam([
      'sim',
      'hospital',
    ]);
    if(!$request['sim'] && !$request['hospital']){
      $request = file_get_contents('php://input');
      $request = json_decode($request,true);
    }
    $result = DeviceLogic::miniList($request);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, $result);
    }
  }
  public function read($id)
  {
    $this->validateSim($id);
    $result = DeviceLogic::miniDetail($id);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

  public function save(){
    $request = $this->selectParam([
      'sim', // SIM号
      'hospital',//
    ]);
    $result = DeviceLogic::miniAdd($request);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, [$result]);
    }
  }

}
