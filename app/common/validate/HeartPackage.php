<?php

namespace app\common\validate;

use think\Validate;

/**
 * 心跳包-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class HeartPackage extends Validate
{
  protected $rule = [
    'sim' => 'require',
    'file' => 'require'
  ];

  protected $field = [
    'sim' => 'SIM号',
    'file' => '文件',
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['sim', 'file'],
    'edit' => [],
  ];
}
