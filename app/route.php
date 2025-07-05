<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//use app\api\logic\common\UploadBase64Logic;
use think\Request;
use think\Route;
use app\api\model\Device;
use app\api\model\DeviceLog;
use app\api\model\Heart;
use app\api\model\DeviceDay;

Route::get('CheckStatus',function (){
  $info = Device::build()->field('uuid,heart,sim')->where('state',2)->where('status','<>',5)->where('status','<>',0)->select();
  foreach($info as $v){
    $time = $v->heart*2*60;
    $end = now_time(time());
    $start = date('Y-m-d H:i:s',time() - $time);
    $week =  date('w',time());
    if($week == 6 || $week == 0){
      //如果是周末
      $end = date('Y-m-d', strtotime("this week Friday", time())).' 24:59:59';
      $start = date('Y-m-d H:i:s',strtotime($end) - $time);
    }
    //周日到（周一+2次心跳间隔）之间
    if(strtotime($end) - strtotime("this week Monday", time()) < $time){
      $start = $start - 2*24*60*60;
    }
    $is = Heart::build()->where('sim',$v->sim)->where('create_time','between time',[$start,$end])->count();
    if($is){
      //在线 距当前时间2次心跳间隔周期内有心跳记录则判断在线 除去周末
      Device::build()->where('uuid',$v->uuid)->update(['status'=>3]);
      //记录添加
      //if(strtotime(DeviceLog::build()::where('sim',$v->sim)->order('create_time','desc')->value('create_time')) < (time()-$v->heart*2*60)){
        DeviceLog::build()->data(['uuid'=>uuid(),'sim'=>$v->sim,'status'=>3])->save();
        echo $v->sim,PHP_EOL;
      //}
    }else{
      $date_time = Heart::build()->where('sim',$v->sim)->order('create_time','desc')->value('create_time');
      $status = '';
      $end = strtotime($end);
      //活跃 最后心跳时间距当前时间在36h内 1次活跃 2活跃 3在线 4离线 除去周末
      if($week == 6 || $week == 0){
        //周末 按本周周五算
        $end = date('Y-m-d', strtotime("this week Friday", time())).' 24:59:59';
        $end = strtotime($end);
        if($end - strtotime($date_time) < 36*60*60){
          $status = 2;
        }
      }
      if($week == 1 && $end - strtotime($date_time) <= 36*60*60+2*24*60*60){
        $status = 2;
      }
      if($week == 2){
        if(strtotime($date_time) - strtotime("this week Tuesday", strtotime($date_time)) < 12*60*60){
          //周二12点前
          if($end - strtotime($date_time) < 36*60*60+2*24*60*60){
            $status = 2;
          }
        }else{
          if($end - strtotime($date_time) < 36*60*60){
            $status = 2;
          }
        }
      }
      if($week > 2 && $week < 6){
        if($end - strtotime($date_time) < 36*60*60){
          $status = 2;
        }
      }
      //次活跃
      if($week == 6 || $week == 0){
        //周末 按本周周五算
        $end = date('Y-m-d', strtotime("this week Friday", time())).' 24:59:59';
        $end = strtotime($end);
        if($end - strtotime($date_time) < 168*60*60+2*24*60*60 && $end - strtotime($date_time) > 36*60*60){
          $status = 1;
        }
      }
      if($week == 1){
        if($end - strtotime($date_time) > 36*60*60+2*24*60*60 && $end - strtotime($date_time) < 168*60*60+4*24*60*60){
          $status = 1;
        }
      }
      if($week == 2){
        if(strtotime($date_time) - strtotime("this week Tuesday", strtotime($date_time)) < 12*60*60){
          //周二12点前
          if($end - strtotime($date_time) > 36*60*60+2*24*60*60 && $end - strtotime($date_time) < 168*60*60+4*24*60*60){
            $status = 1;
          }
        }else{
          if($end - strtotime($date_time) > 36*60*60 && $end - strtotime($date_time) < 168*60*60+4*24*60*60){
            $status = 1;
          }
        }
      }
      if($week >= 3 && $week < 6){
        if($end - strtotime($date_time) > 36*60*60 && $end - strtotime($date_time) < 168*60*60+2*24*60*60){
          $status = 1;
        }
      }
      //离线
      if($week == 6 || $week == 0){
        //周末 按本周周五算
        $end = date('Y-m-d', strtotime("this week Friday", time())).' 24:59:59';
        $end = strtotime($end);
        if($end - strtotime($date_time) > 168*60*60+2*24*60*60){
          $status = 4;
        }
      }
      if($week > 0 && $week <3){
        if($end - strtotime($date_time) > 168*60*60+4*24*60*60){
          $status = 4;
        }
      }
      if($week>=3 && $week <6){
        if($end - strtotime($date_time) > 168*60*60+2*24*60*60){
          $status = 4;
        }
      }
      if($status){
        //if(strtotime(DeviceLog::build()::where('sim',$v->sim)->order('create_time','desc')->value('create_time')) < (time()-$v->heart*2*60)){
          Device::build()->where('uuid',$v->uuid)->update(['status'=>$status]);
          DeviceLog::build()->data(['uuid'=>uuid(),'sim'=>$v->sim,'status'=>$status])->save();
          echo $v->sim,PHP_EOL;
        //}
      }
    }
  }

  //早上十点半结算当天设备数
  $week = date('w');
  if(!in_array($week,[6,0])){
    $times = date('H:i');
    if($times >= '10:30'){
      $is = DeviceDay::build()->where('day_time',date('Y-m-d'))->count();
      if(!$is){
        //保存新数据
        $data = DeviceDay::build();
        $data->uuid = uuid();
        $data->day_time = date('Y-m-d');
        $data->total = Device::build()->count();
        $data->online = Device::build()->where('status',3)->count();
        $data->offline = Device::build()->where('status','in',[1,2,4])->count();
        $data->trouble = Device::build()->where('status',5)->count();
        $data->save();
      }
    }
  }
});

// 公共
Route::group(':version/common', function () {
    // 创建模板
    Route::resource('CreateTemplate', 'api/:version.common.CreateTemplate');
    //定时器用
    Route::resource('Crontab', 'api/:version.common.Crontab');
    // 快递100-物流信息
    Route::resource('ExpressInfo', 'api/:version.common.ExpressInfo');
    // 快递100-快递公司
    Route::resource('ExpressCompany', 'api/:version.common.ExpressCompany');
    // 获取用户手机号
    Route::resource('FetchUserPhone', 'api/:version.common.FetchUserPhone');
    // 获取小程序二维码
    Route::resource('FetchQRCode', 'api/:version.common.FetchQRCode');
    // 获取小程序码
    Route::resource('GetQRCode', 'api/:version.common.GetQRCode');
    // 经纬度逆解析
    Route::resource('Geocoder', 'api/:version.common.Geocoder');
    // 地区
    Route::resource('Region', 'api/:version.common.Region');
    //发送短信验证码
    Route::resource('SendSmsCode', 'api/:version.common.SendSmsCode');
    //上传文件
    Route::resource('Upload', 'api/:version.common.Upload');
    //上传文件，返回文件大小
    Route::resource('UploadFile', 'api/:version.common.UploadFile');
    //上传Base64文件
    Route::resource('UploadBase64', 'api/:version.common.UploadBase64');
    // UE富文本编辑器文件上传
    Route::resource('UEUpload', 'api/:version.common.UEUpload');
    // 统一下单
    Route::resource('UnionOrderPayment', 'api/:version.common.UnionOrderPayment');
    // 统一下单-支付回调-用户端
    Route::resource('UnionOrderPaymentNotify', 'api/:version.common.UnionOrderPaymentNotify');
    // 统一下单-订单查询
    Route::resource('UnionOrderQuery', 'api/:version.common.UnionOrderQuery');
    // 统一下单-订单退款
    Route::resource('UnionOrderRefund', 'api/:version.common.UnionOrderRefund');
    // 微信登录
    Route::resource('WechatLogin', 'api/:version.common.WechatLogin');
    // 苹果登录
    Route::resource('IosLogin', 'api/:version.common.IosLogin');
    // 皮卡图片处理
    Route::resource('PicupShop', 'api/:version.common.PicupShop');
    // 腾讯电子签
    Route::resource('Ess', 'api/:version.common.Ess');
    // 腾讯电子签-回调
    Route::resource('EssCallback', 'api/:version.common.EssCallback');
    // 【临时接口】根据手机号清理用户信息
    Route::resource('ClearUser', 'api/:version.common.ClearUser');
    //密码登录
    Route::resource('LoginByPassword', 'api/:version.common.LoginByPassword');
    //验证码登录
    Route::resource('LoginByCode', 'api/:version.common.LoginByCode');
    //获取手机号
    Route::resource('GetMobile', 'api/:version.common.GetMobile');
    //获取手机号
    Route::resource('log', 'api/:version.common.Log');
    //获取二维码
    Route::resource('GetAppQrCode', 'api/:version.common.GetAppQrCode');
    //获取选手详情
    Route::resource('Contestant', 'api/:version.common.Contestant');
    //小码短链接
    Route::resource('LinkUrl', 'api/:version.common.LinkUrl');
    //oss视频地址下载
    Route::resource('VideoDownLoad', 'api/:version.common.VideoDownLoad');
});

// 硬件端
Route::group(':version/mini', function () {
    // 安装包
    Route::resource('Package', 'api/:version.mini.Package');
    //设备
    Route::resource('Device', 'api/:version.mini.Device');
    //心跳包
    Route::resource('HeartPackage', 'api/:version.mini.HeartPackage');
    //心跳
    Route::resource('Heart', 'api/:version.mini.Heart');
    //故障记录
    Route::resource('Trouble', 'api/:version.mini.Trouble');
    //试剂余量
    Route::resource('Reagent', 'api/:version.mini.Reagent');
    //试剂更换记录
    Route::resource('ReagentChange', 'api/:version.mini.ReagentChange');
    //质检-校准
    Route::resource('CheckCalibration', 'api/:version.mini.CheckCalibration');
    //维修日志
    Route::resource('ServiceLog', 'api/:version.mini.ServiceLog');
});

// 管理端
Route::group(':version/cms', function () {
    // 后台登录
    Route::resource('Login', 'api/:version.cms.Login');
    //菜单
    Route::resource('AdminMenu', 'api/:version.cms.AdminMenu');
    //角色
    Route::resource('AdminRole', 'api/:version.cms.AdminRole');
    //管理员
    Route::resource('Admin', 'api/:version.cms.Admin');
    //日志
    Route::resource('AdminLog', 'api/:version.cms.AdminLog');
    //日志类型
    Route::resource('AdminLogType', 'api/:version.cms.AdminLogType');
    //导出日志操作
    Route::resource('AdminLogExport', 'api/:version.cms.AdminLogExport');
    //区域管理
    Route::resource('Area', 'api/:version.cms.Area');
    //区域树管理
    Route::resource('AreaTree', 'api/:version.cms.AreaTree');
    //省份管理
    Route::resource('Province', 'api/:version.cms.Province');
    //城市管理
    Route::resource('City', 'api/:version.cms.City');
    //设备管理
    Route::resource('Device', 'api/:version.cms.Device');
    //设备管理导出
    Route::resource('DeviceExport', 'api/:version.cms.DeviceExport');
    //安装包管理
    Route::resource('Package', 'api/:version.cms.Package');
    //获取已有医院/设备型号
    Route::resource('Option', 'api/:version.cms.Option');
    //验证码
    Route::resource('Captcha', 'api/:version.cms.Captcha');
    //试剂余量
    Route::resource('Reagent', 'api/:version.cms.Reagent');
    //心跳记录
    Route::resource('Heart', 'api/:version.cms.Heart');
    //故障记录
    Route::resource('Trouble', 'api/:version.cms.Trouble');
    //心跳包
    Route::resource('HeartPackage', 'api/:version.cms.HeartPackage');
    //质检-校准
    Route::resource('CheckCalibration', 'api/:version.cms.CheckCalibration');
    //试剂更换记录
    Route::resource('ReagentChange', 'api/:version.cms.ReagentChange');
    //样式测试结果
    Route::resource('ReagentTest', 'api/:version.cms.ReagentTest');
    //试剂更换记录统计
    Route::resource('ReagentChangeStatistics', 'api/:version.cms.ReagentChangeStatistics');
    //维修日志
    Route::resource('ServiceLog', 'api/:version.cms.ServiceLog');
    //设备统计
    Route::resource('DeviceStatistics', 'api/:version.cms.DeviceStatistics');
    //样本测试统计
    Route::resource('ReagentTestStatistics', 'api/:version.cms.ReagentTestStatistics');
});



Route::miss('Error/index');
$request = Request::instance();
if ($request->method() === "OPTIONS") {
    exit(json_encode(array('error' => 200, 'message' => 'option true.')));
} elseif ($request->method() === "HEAD") {
    exit(json_encode(array('error' => 200, 'message' => 'option true.')));
}
return [
    '__pattern__' => [
        'name' => '\w+',
    ],


];
