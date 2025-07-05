<?php

namespace app\api\model;

use Exception;

/**
 * 合伙人用户-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class Partner extends BaseModel
{
    public static function build()
    {
        return new self();
    }

    /**
     * 生成ID号
     */
    public function createID()
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

    /**
     * 用户登陆
     * @param {String} $mobile 账号
     * @param {String} $password 密码
     */
    public function login($mobile, $password)
    {
        // 加密密码
        $password = md6($password);

        // 用户登陆
        $user = self::field('*')
            ->where(['mobile' => $mobile, 'password' => $password, 'is_deleted' => 1])
            ->find();

        // 如果用户不存在，则报错
        if (empty($user)) {
            AdminLog::build()->add($user['uuid'], '登陆失败');
            throw new Exception('登陆失败', 403);
        }

        // 用户禁用
        if ($user['disabled'] == 2) {
            AdminLog::build()->add($user['uuid'], '登陆失败');
            throw new Exception('登陆失败,该用户被禁用', 403);
        }

        $user['last_login'] = now_time(time());
        $user->save();

        AdminLog::build()->add($user['uuid'], '登陆成功');

        $user = objToArray($user);
        unset($user['password']);

        // 普通管理员
        if ($user['type'] == 1) {
            // 查询角色
            $adminRole = AdminRole::build()->where(['uuid' => $user['role_uuid']])->find();
            // 获取用户权限
            $user['menus'] = $adminRole['menus'];
            $menus = AdminMenu::build()->field('uuid id,name,pid,level')->where(['uuid' => ['in', $adminRole['menus']], 'is_deleted' => 1])->field('uuid,name,url,pid')->select();
            // 角色名
            $user['role_name'] = $adminRole['name'];
        }
        // 超级管理员
        else {
            // 角色名
            $user['role_name'] = '超级管理员';
            // 获取用户权限
            $user['menus'] = AdminMenu::build()->where(['is_deleted' => 1])->column('uuid');
            $menus = AdminMenu::build()->field('uuid id,name,pid,level')->where(['is_deleted' => 1])->field('uuid,name,url,pid')->select();
        }

        $menus = objToArray($menus);
        $user['menus_all'] = getTreeList($menus, null);

        // 记录用户信息
        $result['user'] = $user;

        // 更新Token
        $token = AdminToken::build()->where(['admin_uuid' => $user['uuid']])->find();
        if (empty($token)) {
            throw new Exception('非法登陆', 403);
        }

        // 如果Token已过期，则更新Token
        if ($token->expiry_time < now_time(time())) {
            // 生成Token
            $result['token'] = uuid();
            $token->token = $result['token'];
            $token->expiry_time = date("Y-m-d H:i:s", time() + 604800);
            $token->save();
        }

        $result['token'] = $token->token;


        return $result;
    }
}
