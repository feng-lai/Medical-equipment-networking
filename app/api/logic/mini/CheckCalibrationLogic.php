<?php

namespace app\api\logic\mini;

use app\common\tools\AliOss;
use think\Exception;
use think\Db;
use app\api\model\CheckCalibration;
/**
 * 质检-校准-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class CheckCalibrationLogic
{


  static public function miniAdd($request)
  {
    try {
      $request['uuid'] = uuid();
      CheckCalibration::build()->data($request)->save();
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }


}
