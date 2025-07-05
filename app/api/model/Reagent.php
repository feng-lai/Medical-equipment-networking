<?php

namespace app\api\model;

use Exception;

/**
 * 试剂余量-模型
 * User: Yacon
 * Date: 2022-07-20
 * Time: 13:19
 */
class Reagent extends BaseModel
{
    public static function build()
    {
        return new self();
    }

    public function setReagentAAttr($value)
    {
      return json_encode($value);
    }

    public function getReagentAAttr($value)
    {
      return json_decode($value,true);
    }

    public function setReagentBAttr($value)
    {
      return json_encode($value);
    }

    public function getReagentBAttr($value)
    {
      return json_decode($value,true);
    }

    public function setReagentCAttr($value)
    {
      return json_encode($value);
    }
    public function getReagentCAttr($value)
    {
      return json_decode($value,true);
    }

    public function setReagentHAttr($value)
    {
      return json_encode($value);
    }
    public function getReagentHAttr($value)
    {
      return json_decode($value,true);
    }

    public function setChrCloAttr($value)
    {
      return json_encode($value);
    }
    public function getChrCloAttr($value)
    {
      return json_decode($value,true);
    }
}