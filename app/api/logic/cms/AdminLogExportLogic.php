<?php

namespace app\api\logic\cms;

use app\api\model\AdminLog;
use app\api\model\AdminToken;
use app\api\model\AdminRole;
use app\common\tools\AliOss;
use think\Exception;
use think\Db;

/**
 * 后台用户-逻辑
 * User: Yacon
 * Date: 2022-08-11
 * Time: 21:24
 */
class AdminLogExportLogic
{
  static public function cmsList($request)
  {
    $map['l.is_deleted'] = 1;
    if($request['action']) $map['l.action'] = ['>=',$request['action']];
    if($request['start_time']) $map['l.create_time'] = ['between time',[$request['start_time'],$request['end_time'].'23:59:59']];
    if($request['keyword_search']) $map['l.explain|l.action'] = ['like','%'.$request['keyword_search'].'%'];
    $result = AdminLog::build()
      ->field('l.uuid,a.mobile,l.action,l.explain,l.create_time,l.ip')
      ->alias('l')
      ->join('admin a','a.uuid = l.admin_uuid')
      ->where($map)
      ->order('l.create_time desc')
      ->paginate(['list_rows' => $request['page_size'], 'page' => $request['page_index']]);
    $data = [];
    $data[] = ['账号', '操作类型', '说明','操作时间','登录ip'];
    foreach ($result as $k => $v) {
      $tmp = [
        $v->mobile,
        $v->action,
        $v->explain,
        $v->create_time,
        $v->ip
      ];

      foreach ($tmp as $tmp_k => $tmp_v) {
        $tmp[$tmp_k] = $tmp_v.'';
      }
      $data[] = $tmp;
    }

    try{
      $excel = new \PHPExcel();
      $excel_sheet = $excel->getActiveSheet();
      $excel_sheet->fromArray($data);
      $excel_writer = \PHPExcel_IOFactory::createWriter($excel,'Excel2007');

      $file_name = '操作日志数据.xlsx';
      $file_path = ROOT_PATH .$file_name;
      $excel_writer->save($file_path);

      if (!file_exists($file_path)) {
        throw new \Exception("Excel生成失败");
      }
      //$result = uploadFileExcel($file_name,$file_path,'match_service/excel/');
      $oss = new AliOss();
      $oss->uploadOss($file_path, 'match_service/excel/'.$file_name);
      unlink($file_path);
      return 'match_service/excel/'.$file_name;
    } catch (\Exception $e) {
      unlink($file_path);
      throw new Exception($e->getMessage(), 500);
    }
  }

  static public function cmsDetail($id)
  {
    return  Admin::build()
      ->where('uuid', $id)
      ->where('is_deleted','=',1)
      ->field('*')
      ->find();
  }

   static public function cmsAdd($request){
     try {
       Db::startTrans();
       AdminRole::build()->findOrFail($request['role_uuid']);
       if(Admin::build()->where('mobile',$request['mobile'])->count()){
         throw new Exception('账号已存在', 500);
       }
       $data = [
         'uuid' => uuid(),
         'name'=>$request['name'],
         'password'=>md6($request['password']),
         'email'=>$request['email'],
         'mobile'=>$request['mobile'],
         'role_uuid'=>$request['role_uuid'],
         'create_time' => now_time(time()),
         'update_time' => now_time(time()),
       ];
       Admin::build()->save($data);
       //添加token
       $token = AdminToken::build();
       $token->uuid = uuid();
       $token->admin_uuid = $data['uuid'];
       $token->create_time = now_time(time());
       $token->update_time = now_time(time());
       $token->save();
       Db::commit();
       return $data['uuid'];
     } catch (Exception $e) {
       Db::rollback();
       throw new Exception($e->getMessage(), 500);
     }
   }

  static public function cmsEdit($request)
  {
    try {
      $user = Admin::build()->where('uuid', $request['uuid'])->find();
      if(isset($request['password'])){
        $request['password'] = md6($request['password']);
      }
      $user->save($request);
      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage(), 500);
    }
  }

   static public function cmsDelete($id){
     try {
       AdminLog::build()->where('uuid',$id)->update(['is_deleted'=>2]);
       return true;
     } catch (Exception $e) {
         throw new Exception($e->getMessage(), 500);
     }
   }
}
