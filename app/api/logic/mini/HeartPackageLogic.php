<?php

namespace app\api\logic\mini;

use app\api\model\HeartPackage;
use app\api\model\Device;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 心跳包-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class HeartPackageLogic
{


  static public function miniAdd($request)
  {
    try {
      DB::startTrans();
      //文件上传
      $file = $request['file'];
      //$arr = file_get_contents($file->getInfo()['tmp_name']);
      //print_r(base64_encode($arr));exit;
      //$filepath = 'upload'. DS .date('Ymd'). DS .uuid().'.zip';
      //$is = file_put_contents(ROOT_PATH . 'public' . DS . $filepath,base64_decode($request['file']));
      //$filepath = str_replace('\\', '/', $filepath);
      $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
      empty($info) ? $this->returnmsg(403, [], [], 'Forbidden', '', $file->getError()) : '';
      $filePath = str_replace('\\', '/', $info->getSaveName());
      HeartPackage::build()->insert([
        'uuid'=>uuid(),
        'file'=>'upload/'.$filePath,
        'sim'=>$request['sim'],
        'create_time'=>now_time(time()),
        'update_time'=>now_time(time())
      ]);
      Device::build()->where('sim',$request['sim'])->update(['is_heart'=>0]);
      DB::commit();
      return true;
    } catch (Exception $e) {
      DB::rollback();
      throw new Exception($e->getMessage(), 500);
    }
  }


}
