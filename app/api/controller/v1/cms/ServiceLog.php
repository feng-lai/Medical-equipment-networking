<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\ServiceLogLogic;

/**
 * 维修日志-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class ServiceLog extends Api
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
      'start_time',
      'end_time'
    ]);
    $result = ServiceLogLogic::cmsList($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function read($id)
  {
    $result = ServiceLogLogic::cmsDetail($id,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function save()
  {
    $request = $this->selectParam([
      "sim",
      "name",
      "service_type",
      "department",
      "start_time",
      "end_time",
      "hospital",
      "hospital_address",
      "hospital_contact",
      "hospital_mobile",
      "install_time",
      "device_type",
      "device_snum",
      "device_v",
      "device_img",
      "reagent_snum",
      "reagent_lot_num",
      "power_up",
      "day_min",
      "day_max",
      "year_min",
      "year_max",
      "rate",
      "part",
      "dsc",
      "b_fail_operate",
      "handle",
      "sevice_rate",
      "is_change",
      "is_solve",
      "install_is_suc",
      "sales_sevice_freg",
      "sales_content",
      "more_sevice_con",
      "model_num",
      "sevice_num"
    ]);
    $this->check($request, "ServiceLog.save");
    $result = ServiceLogLogic::cmsAdd($request,$this->userInfo);
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
    $result = ServiceLogLogic::cmsEdit($request,$this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

  public function delete($id)
  {
    $result = ServiceLogLogic::cmsDelete($id,$this->userInfo);
    if (isset($result['msg'])) {
      $this->returnmsg(400, [], [], '', '', $result['msg']);
    } else {
      $this->render(200, ['result' => $result]);
    }
  }

}
