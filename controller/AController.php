<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-9
 * Time: 下午9:36
 */
namespace tmf\controller;

abstract class AController{
    protected $view_suffix = '.php';            //视图文件后缀（.php;.tpl;.html）
    protected $filter = array();                //过滤器配置，一个数组
    public  $layout = false;               //是否加载布局文件
    public  $defaultAction = 'index';                //默认行为

    public abstract function getCurrentActionFilterName();              //获取当前行为的$filter名称，一个数组
    public abstract function createUrl($base_info,$data = null);        //创建一个相对路径
    public abstract function createAbsoluteUrl($base_info,$data=null);  //创建一个绝对路径

    public abstract function beforeAction();                            //在开始行为前需要做的事情
    public abstract function render($base_info,$data=null,$return=false); //渲染视图

    public abstract function forward($base_info,$data=null);            //请求其他控制器的方法资源
    public abstract function redirectByUrl($url,$terminate=true, $statusCode=302);                       //通过url跳转
    public abstract function redirect($base_info,$data=null,$absolute = true, $terminate = true, $statusCode = 302); //跳转到该域下
}