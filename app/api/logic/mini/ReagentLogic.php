<?php

namespace app\api\logic\mini;

use app\common\tools\AliOss;
use think\Exception;
use think\Db;
use app\api\model\Reagent;
use app\api\model\ReagentDay;
/**
 * 试剂余量记录-逻辑
 * User: Yacon
 * Date: 2022-07-21
 * Time: 14:31
 */
class ReagentLogic
{


  static public function miniAdd($request)
  {
    try {

      DB::startTrans();
      if($request['reagent_a']){
        $reagent_a = explode('\\',$request['reagent_a']);
        $request['reagent_a'] = ['all'=>$reagent_a[1],'remain'=>$reagent_a[0]];
      }
      if($request['reagent_b']){
        $reagent_b = explode('\\',$request['reagent_b']);
        $request['reagent_b'] = ['all'=>$reagent_b[1],'remain'=>$reagent_b[0]];
      }
      if($request['reagent_c']){
        $reagent_c = explode('\\',$request['reagent_c']);
        $request['reagent_c'] = ['all'=>$reagent_c[1],'remain'=>$reagent_c[0]];
      }
      if($request['reagent_h']){
        $reagent_h = explode('\\',$request['reagent_h']);
        $request['reagent_h'] = ['all'=>$reagent_h[1],'remain'=>$reagent_h[0]];
      }
      if($request['chr_clo']){
        $chr_clo = explode('\\',$request['chr_clo']);
        $request['chr_clo'] = ['all'=>$chr_clo[1],'remain'=>$chr_clo[0]];
      }
      if(!strtotime($request['test_time'])){
        unset($request['test_time']);
        if(!$request['blood_sugar']){
          unset($request['blood_sugar']);
        }
        if(!$request['lactate']){
          unset($request['lactate']);
        }
        if(!$request['res_val']){
          unset($request['res_val']);
        }
        if(!$request['sum']){
          unset($request['sum']);
        }
        if(!$request['number']){
          unset($request['number']);
        }
        if(!$request['reagent_a']){
          unset($request['reagent_a']);
        }
        if(!$request['reagent_b']){
          unset($request['reagent_b']);
        }
        if(!$request['reagent_c']){
          unset($request['reagent_c']);
        }
        if(!$request['reagent_h']){
          unset($request['reagent_h']);
        }
        if(!$request['chr_clo']){
          unset($request['chr_clo']);
        }
        if(!$request['type']){
          unset($request['type']);
        }
        if(!$request['par2']){
          unset($request['par2']);
        }
        if(!$request['area']){
          unset($request['area']);
        }
        if(!$request['reserve1']){
          unset($request['reserve1']);
        }
        if(!$request['reserve2']){
          unset($request['reserve2']);
        }
        if(!$request['reserve3']){
          unset($request['reserve3']);
        }
        if(!$request['reserve4']){
          unset($request['reserve4']);
        }
        if(!$request['reserve5']){
          unset($request['reserve5']);
        }
        if(!$request['origin']){
          unset($request['origin']);
        }
        $device = Reagent::build()->where('sim',$request['sim'])->order('test_time','desc')->find();
        if($device) $device->save($request);
      }else{
        $request['uuid'] = uuid();
        Reagent::build()->save($request);
        //数据统计插入
        $data = ReagentDay::build()->where('test_time',date('Y-m-d',strtotime($request['test_time'])))->where('sim',$request['sim'])->find();
        if(!$data){
          //新建
          $data = ReagentDay::build();
          $data->uuid = uuid();
          $data->sim = $request['sim'];
        }else{
          //更新
          if($request['blood_sugar']){
            $data->blood_sugar = $data->blood_sugar+1;
          }
          if($request['lactate']){
            $data->lactate = $data->lactate+1;
          }
        }
        $data->sum = $request['sum'];
        $data->origin = $request['origin'];
        $data->test_time = $request['test_time'];
        $data->save();
      }
      DB::commit();
      return true;
    } catch (Exception $e) {
      DB::rollback();
      throw new Exception($e->getMessage(), 500);
    }
  }

}
