<?php

namespace app\api\model;

use Exception;

/**
 * 故障记录-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class Trouble extends BaseModel
{
    public static function build()
    {
        return new self();
    }
}