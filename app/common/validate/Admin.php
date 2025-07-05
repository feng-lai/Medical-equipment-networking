<?php

namespace app\common\validate;

use think\Validate;

/**
 * 后台用户-校验
 * User:
 * Date: 2022-07-20
 * Time: 13:25
 */
class Admin extends Validate
{
  protected $rule = [
    'name' => 'require',
    'mobile' => 'require',
    'password' => 'require',
    'uname' => 'require',
    'role_uuid' => 'require',
    'province_uuid'=>'require',
    'area_uuid'=>'require',
    'city_uuid'=>'require'
  ];

  protected $field = [
    'name' => '名称',
    'mobile' => '手机号',
    'password' => '密码',
    'uname' => '账号',
    'role_uuid' => '角色uuid',
    'province_uuid'=>'省份uuid',
    'area_uuid'=>'区域uuid',
    'city_uuid'=>'城市uuid'
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['name', 'uname','password','mobile','role_uuid','province_uuid','area_uuid','city_uuid'],
    'edit' => [],
  ];
}
