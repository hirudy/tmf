<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-7
 * Time: 下午12:36
 */
namespace tmf\web;

use tmf\controller\Controller;
use tmf\db\mysql\mysqlDb;
use tmf\lib\ErrorTip;
use tmf\lib\HttpRequest;
use tmf\Tmf;
use tmf\component\identity\WebUser;
use tmf\web\dispatcher\NormalDispatcher;

class Application{
    /**
     * 分析配置文件
     * @param $config array 配置数组
     */
    private function analyseConfig($config){
        //导入配置文件
        $absolute_path = App::$http_request->getAbsoluteBasePath().'/'.$config;
        $arr_config = require $absolute_path;
        // 是否为调试模式
        App::$debug = isset($arr_config['debug']) === true?$arr_config['debug']:false;
        if(!App::$debug)
            error_reporting(0);
        App::$errorConAct = (isset($arr_config['errorHandler']) && is_string($arr_config['errorHandler']))?$arr_config['errorHandler']:'';
        App::$protected_name = substr($config,0,strpos($config,'/'));
        App::$app_config = $arr_config;
        App::$default_controller = isset($arr_config['defaultController'])?strtolower($arr_config['defaultController']):'index';
        App::$url_manager = $this->createUrlManager($arr_config);
        App::$db = $this->createDb($arr_config);
        App::$user = new WebUser();
    }

    /**
     * 创建URL对象；
     * @param $config
     * @return bool|object
     */
    private function createUrlManager($config){
        $default = 'tmf\web\urlManager\NormalUrl';
        if(!isset($config['urlManager']))
            return new $default();
        $reflect_class = new \ReflectionClass($config['urlManager']);
        if($reflect_class->isSubclassOf('tmf\web\urlManager\UrlManager'))
            return $reflect_class->newInstanceArgs();
        else{
            ErrorTip::Error('配置文件中的urlManager不是tmf\web\urlManager\UrlManager的子类',__FILE__);
            return null;
        }
    }

    /**
     * 创建一个数据库操作对象
     * @param $config
     * @return bool
     */
    private function createDb($config){
        $rel = null;
        $isRightConfig = false;
        //判断配置是否正确
        do{
            if(!isset($config['db']))
                break;
            $key = $config['db'];
            if(!isset($key['type']) || !isset($key['one']))
                break;
            $key2 =$key['one'];
            if(!isset($key2['connectionString']) || !isset($key2['username']) || !isset($key2['password']) || !isset($key2['charset']))
                break;
            $isRightConfig = true;
        }while(false);
        //创建一个数据库操作对象
        if($isRightConfig == true){
            switch($config['db']['type']){
                case 'mysql':
                    $rel = new mysqlDb();
                    break;
                default:
                    break;
            }
        }
        return $rel;
    }

    public function __construct($config){
        //分析请求头部
        App::$http_request = new HttpRequest();
        //保存应用根目录
        Tmf::$appPath = App::$http_request->getAbsoluteBasePath();
        //分析配置文件
        $this->analyseConfig($config);

        //设置内部字符编码-utf-8;
        mb_internal_encoding('utf-8');
        //开启session,自动登陆模块
        session_set_cookie_params(0,App::$http_request->getBaseUrl());
        session_start();
        App::$user->loginFromCookie();
    }

    //运行应用
    public function run(){
        $dispatcher = new NormalDispatcher();
        App::$dispatcher = $dispatcher;
        $dispatcher->dispatch();
    }

    /**
     * 结束运行程序
     */
    public static function end(){
        exit;
    }
}