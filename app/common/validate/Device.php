<?php

namespace app\common\validate;

use think\Validate;

/**
 * 设备-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class Device extends Validate
{
  protected $rule = [
    'sim' => 'require',
    'type' => 'require',
    'hospital' => 'require',
    'contact' => 'require',
    'area_uuid' => 'require',
    'province_uuid' => 'require',
    'city_uuid' => 'require',
    'mobile' => 'require',
    'address' => 'require',
    'phone'=>'require'
  ];

  protected $field = [
    'sim' => 'SIM号',
    'type' => '设备类型',
    'hospital' => '归属医院',
    'contact' => '医院联系人',
    'area_uuid' => '区',
    'province_uuid' => '省',
    'city_uuid' => '市',
    'mobile' => '预警手机号',
    'address' => '坐在地址',
    'phone' => '医院联系人电话',
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['sim', 'type','hospital', 'contact','area_uuid', 'province_uuid','city_uuid', 'mobile', 'address','phone'],
    'edit' => [],
  ];
}
