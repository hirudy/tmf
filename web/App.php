<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-7
 * Time: 下午3:43
 */
namespace tmf\web;

//定义全局变量
class App{
    public static $http_request;          //请求信息（解析$_SERVER）
    public static $debug;                 //是否是调试模式，非调试模式将关闭错误报告
    public static $errorConAct;           //出错控制器与行为(如：site/error)
    public static $protected_name;        //受保护的代码的父文件夹名称
    public static $app_config ;           //全局的配置文件
    public static $default_controller;    //默认控制器
    public static $url_manager;           //初始化当前url的样式
    public static $controller ;           //当前请求的控制器名称
    public static $db;                    //数据库操作对象
    public static $user;                  //当前用户
    public static $dispatcher;            //当前分发器
    public static $default_action;        //默认行为
    public static $action;                //当前请求的行为名称

    //获取代码受保护的文件夹路径（包含控制器，视图，模型层等）
    public static function getProtectedDir(){
        return (self::$http_request->getAbsoluteBasePath()).'/'.self::$protected_name;
    }
}