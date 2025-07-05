<?php

namespace app\api\controller\v1\mini;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\mini\ServiceLogLogic;

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
      "type"=>2,
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

}
