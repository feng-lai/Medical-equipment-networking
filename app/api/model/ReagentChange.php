<?php

namespace app\api\model;

use Exception;

/**
 * 试剂更换记录-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class ReagentChange extends BaseModel
{
    public static function build()
    {
        return new self();
    }
}