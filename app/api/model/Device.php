<?php

namespace app\api\model;

/**
 * 设备管理-模型
 * User:
 * Date:
 * Time:
 */
class Device extends BaseModel
{
    public static function build()
    {
        return new self();
    }

    public function vali($sim){
      if($this->where('sim',$sim)->value('state') != 2){
        return false;
      }else{
        return true;
      }
    }

}
