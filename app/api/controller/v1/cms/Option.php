<?php

namespace app\api\controller\v1\cms;

use app\api\controller\Api;
use think\Exception;
use app\api\logic\cms\PackageLogic;
use app\api\model\Device;
use app\api\model\Trouble;

/**
 * 获取医院/设备型号-控制器
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class Option extends Api
{
  public $restMethodList = 'get';


  public function _initialize()
  {
    parent::_initialize();
    $this->userInfo = $this->cmsValidateToken();
  }

  public function index()
  {
    $request = $this->selectParam([
      'sim',
      'type'
    ]);
    $map = [];
    //$sim = get_sim($this->userInfo['uuid']);
    //if($sim) $map['sim'] = ['in',$sim];
    $request['sim']?$map['sim'] = $request['sim']:'';
    $data = Device::build()->where($map);
    switch ($request['type']){
      case 1:
        //医院
        $result = $data->group('hospital')->where($map)->column('hospital');
      break;
      case 2:
        //设备型号
        $result = $data->group('type')->where($map)->column('type');
        break;
      case 3:
        //故障代码
        $result = Trouble::build()->where($map)->group('code')->column('code');
        break;
    }
    $result = array_values(array_filter($result,'strlen'));
    $this->render(200, ['result' => $result]);
  }

}
