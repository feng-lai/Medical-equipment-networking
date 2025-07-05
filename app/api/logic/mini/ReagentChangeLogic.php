<?php

namespace app\api\logic\mini;

use app\common\tools\AliOss;
use think\Exception;
use think\Db;
use app\api\model\ReagentChange;
/**
 * 试剂更换记录-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class ReagentChangeLogic
{


  static public function miniAdd($request)
  {
    try {
      $heart = ReagentChange::build();
      $heart->uuid = uuid();
      $heart->sim = $request['sim'];
      $heart->type = $request['type'];
      $heart->validity = $request['validity'];
      $heart->contains_t = $request['contains_t'];
      $heart->contains_ml = $request['contains_ml'];
      $heart->lot_num = $request['lot_num'];
      $heart->area_code = $request['area_code'];
      $heart->is_new = $request['is_new'];
      $heart->num = ReagentChange::build()->where(['sim'=>$request['sim'],'type'=>$request['type']])->count()+1;
      $heart->change_time = $request['change_time'];
      $heart->reserve1 = $request['reserve1'];
      $heart->reserve2 = $request['reserve2'];
      $heart->save();
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }


}
