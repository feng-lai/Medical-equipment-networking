<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\DeviceLogic;

/**
 * 设备管理-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class Device extends Api
{
  public $restMethodList = 'get|put|post|delete';

  public function _initialize()
  {
    parent::_initialize();
    $this->userInfo = $this->cmsValidateToken();
  }

  public function index()
  {
    $request = $this->selectParam([
      'page_size'=>10,
      'page_index'=>1,
      'sim',
      'area_uuid',
      'hospital',
      'type',
      'state',
      'status'
    ]);
    $result = DeviceLogic::cmsList($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function read($id)
  {
    $result = DeviceLogic::cmsDetail($id,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

   public function save()
   {
     $request = $this->selectParam([
       'sim',
       'type',
       'hospital',
       'contact',
       'area_uuid',
       'province_uuid',
       'city_uuid',
       'mobile',
       'address',
       'phone'
     ]);
     $this->check($request, "Device.save");
     $result = DeviceLogic::cmsAdd($request,$this->userInfo);
     if (isset($result['msg'])) {
       $this->returnmsg(400, [], [], '', '', $result['msg']);
     } else {
       $this->render(200, ['result' => $result]);
     }
   }

  public function update($id)
  {
    $request = $this->selectParam([]);
    $request['uuid'] = $id;
    unset($request['id']);
    unset($request['version']);
    $result = DeviceLogic::cmsEdit($request,$this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

   public function delete($id)
   {
     $result = DeviceLogic::cmsDelete($id,$this->userInfo);
     if (isset($result['msg'])) {
       $this->returnmsg(400, [], [], '', '', $result['msg']);
     } else {
       $this->render(200, ['result' => $result]);
     }
   }
}
