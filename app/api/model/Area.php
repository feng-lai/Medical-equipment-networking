<?php

namespace app\api\model;

use Exception;

/**
 * 区域管理-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class Area extends BaseModel
{
    public static function build()
    {
        return new self();
    }
}
