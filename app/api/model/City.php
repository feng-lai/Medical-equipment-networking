<?php

namespace app\api\model;

use Exception;

/**
 * 城市管理-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class City extends BaseModel
{
    public static function build()
    {
        return new self();
    }
}
