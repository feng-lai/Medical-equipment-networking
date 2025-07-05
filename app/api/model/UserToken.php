<?php

namespace app\api\model;

/**
 * 用户Token-模型
 * User: Yacon
 * Date: 2022-07-21
 * Time: 08:58
 */
class UserToken extends BaseModel
{
    public static function build()
    {
        return new self();
    }


    public function vali($token)
    {
        $time = now_time(time());
        $where = "token='{$token}' and expiry_time>'{$time}'";
        $list = $this->alias('a')->join('user b', 'a.user_uuid=b.uuid')->where($where)->field('b.*')->find();
        if ($list) {
            return $list;
        } else {
            return self::returnmsg(401);
        }
    }
}
