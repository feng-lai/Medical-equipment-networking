<?php

namespace app\api\logic\mini;

use app\common\tools\AliOss;
use think\Exception;
use think\Db;
use app\api\model\ReagentTest;
/**
 * 试剂更换记录-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class ReagentTestLogic
{
  static public function miniAdd($request)
  {
    try {
      $heart = ReagentTest::build();
      $request['uuid'] = uuid();
      $heart->data($request)->save();
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }


}
