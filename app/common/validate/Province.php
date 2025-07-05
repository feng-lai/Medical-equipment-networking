<?php

namespace app\common\validate;

use think\Validate;

/**
 * 省份管理-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class Province extends Validate
{
  protected $rule = [
    'name' => 'require',
    'area_uuid'=>'require'
  ];

  protected $field = [
    'name' => '名称',
    'area_uuid'=>'区域uuid'
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['name','area_uuid'],
    'edit' => [],
  ];
}
