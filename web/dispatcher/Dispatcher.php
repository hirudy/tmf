<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-9
 * Time: 下午9:02
 */
namespace tmf\web\dispatcher;

use tmf\controller\Controller;

abstract class Dispatcher{
    protected $filter;                              //过滤失败时的过滤器
    protected $controller;                          //当前控制器对象
    protected abstract function createController();//创建控制器
    protected abstract function setDefaultAction();//设置默认动作
    protected abstract function doAction(); //执行相应的动作
    protected abstract function isFilter(); //是否能访问该行为，false-不能，true-能
    public    abstract function dispatch();    //分发
    public    abstract function toError();     //跳至用户配置的出错页面。
}