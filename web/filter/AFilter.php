<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-10
 * Time: 上午10:20
 */
namespace tmf\web\filter;

use tmf\lib\ErrorTip;

abstract class AFilter{
    protected  $reason;
    public abstract function isPass();  //是否通过该过滤器，返回boolean,false-不通过，true-通过
    public function getReason(){        //返回不通过原因
        return $this->reason;
    }
    public function errorActon($controller=null){       //处理出错行为
        ErrorTip::warn('filter_error:'.$this->reason,__FILE__);
    }
}