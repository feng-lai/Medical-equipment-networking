<?php
  namespace app\api\model;

  /**
   * 快递100-快递公司-模型
   * User: Yacon
   * Date: 2022-08-31
   * Time: 17:19
   */
  class ExpressCompany extends BaseModel
  {
      public static function build() {
          return new self();
      }
  }