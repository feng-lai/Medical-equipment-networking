<?php

namespace app\api\model;

use Exception;

/**
 * 质检-校准-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class CheckCalibration extends BaseModel
{
    public static function build()
    {
        return new self();
    }
}
