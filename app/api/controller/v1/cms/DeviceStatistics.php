<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\DeviceStatisticsLogic;

/**
 * 设备统计-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class DeviceStatistics extends Api
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
      'area_uuid'
    ]);
    $result = DeviceStatisticsLogic::cmsList($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }

  public function read($id)
  {
    $request = $this->selectParam([
      'start_time',
      'end_time'
    ]);
    $result = DeviceStatisticsLogic::cmsDetail($request,$this->userInfo);
    $this->render(200, ['result' => $result]);
  }
}
