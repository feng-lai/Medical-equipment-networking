<?php

namespace app\api\model;
use \think\Request;

/**
 * 管理员日志-模型
 * User: Yacon
 * Date: 2022-08-11
 * Time: 20:43
 */
class AdminLog extends BaseModel
{
    public static function build()
    {
        return new self();
    }

    /**
     * 添加日志
     * @param {stirng} $admin_uuid 管理员UUID
     * @param {string} $action 操作内容
     * @param {string} $explain 说明
     */
    public function add($admin_uuid, $action, $explain = '')
    {
        $ip = get_client_ip();
        $this->insert([
            'uuid' => uuid(),
            "create_time" => now_time(time()),
            "update_time" => now_time(time()),
            "action" => $action,
            "explain" => $explain,
            "admin_uuid" => $admin_uuid,
            "ip" => $ip,
        ]);
    }

}
