<?php

namespace app\common\validate;

use think\Validate;

/**
 * 安装包管理-校验
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:25
 */
class Package extends Validate
{
  protected $rule = [
    'v' => 'require',
    'file'=>'require',
    'type'=>'require',
  ];

  protected $field = [
    'v' => '版本号',
    'type'=>'类型',
    'file'=>'文件'
  ];

  protected $message = [];

  protected $scene = [
    'list' => [],
    'save' => ['v','type','file'],
    'edit' => [],
  ];

  // 自定义验证规则
  protected function checkType($value,$rule,$data)
  {
    $info = explode('.',$value);
    if($data['type'] == 1){
      //上位机 zip压缩包
      if(end($info) != 'zip'){
        return '上位机安装包格式必须是zip压缩包';
      }

    }else{
      //下位机bin文件
      if(end($info) != 'bin'){
        return '下位机安装包格式必须是bin文件';
      }
    }
    return true;
  }
}
