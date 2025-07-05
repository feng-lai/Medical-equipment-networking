<?php

namespace app\api\model;

use Exception;

/**
 * 心跳记录-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class Heart extends BaseModel
{
    public static function build()
    {
        return new self();
    }
}