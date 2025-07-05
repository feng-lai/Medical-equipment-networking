<?php

namespace app\api\model;

/**
 * 用户列表-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 19:38
 */
class User extends BaseModel
{
    public static function build()
    {
        return new self();
    }

    public function getExtendAttr($value)
    {
        return json_decode($value);
    }

    /**
     * 生成ID号
     */
    public function createUserID()
    {
        $number = $this->max('serial_number');
        $number++;
        $count = strlen($number);
        $pre = 'AM';
        for ($i = 0; $i < 7 - $count; $i++) {
            $pre .= '0';
        }
        $result = $pre .  $number;
        return [$number, $result];
    }
}
