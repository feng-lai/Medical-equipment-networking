<?php

namespace app\api\model;

use Exception;

/**
 * 安装包管理-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class Package extends BaseModel
{
    public static function build()
    {
        return new self();
    }
    public function setFileAttr($value)
    {
      return json_encode($value);
    }
    public function getFileAttr($value)
    {
      return json_decode($value,true);
    }
}
