<?php

namespace app\api\model;

use Exception;

/**
 * 维修日志-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class ServiceLog extends BaseModel
{
    public static function build()
    {
        return new self();
    }
    public function getDeviceImgAttr($value)
    {
      return json_decode($value);
    }

    public function setDeviceImgAttr($value)
    {
      return json_encode($value);
    }

    public function getSalesContentAttr($value)
    {
      return json_decode($value);
    }

    public function setSalesContentAttr($value)
    {
      return json_encode($value);
    }
}
