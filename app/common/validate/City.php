<?php

namespace app\common\validate;

use think\Validate;

/**
 * 城市管理-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class City extends Validate
{
  protected $rule = [
    'name' => 'require',
    'area_uuid'=>'require',
    'province_uuid'=>'require'
  ];

  protected $field = [
    'name' => '名称',
    'area_uuid'=>'区域uuid',
    'province_uuid'=>'省份uuid'
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['name','area_uuid','province_uuid'],
    'edit' => [],
  ];
}
