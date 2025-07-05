<?php

namespace app\api\model;

use Exception;

/**
 * 心跳包-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class HeartPackage extends BaseModel
{
    public static function build()
    {
        return new self();
    }
}