<?php
namespace app\common\model;


use think\Config;
use think\Controller;
use think\Lang;
use think\Model;

/**
* 公用的控制器，pc、app、微信各端不需要控制权限的控制器，必须继承该控制器
 *
* @author json github
* @version 1.0 
*/
class Common extends Model
{
    protected $resultSetType = 'collection';
    protected $autoWriteTimestamp = "datetime";
    protected $dateFormat = 'Y-m-d H:i:s';

    public function setUuidAttr($value)
    {
        return  $value ? $value :uuid();
    }
}