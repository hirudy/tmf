<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-7
 * Time: 上午11:35
 */
namespace tmf;

use tmf\web\Application;

class Tmf{
    public static $tmfVersion;  //框架版本
    public static $tmfPath;     //框架根目录
    public static $appPath;     //应用根目录

    /**
     * 自动加载类文件
     * @param $class_path string 名字命名空间
     */
    private static function autoLoadClass($class_path){
        $file_dir = '';
        $file_name = $class_path;
        $file = '';
        //获取类的路径名称（框架、应用等）
        do{
            if(strpos($class_path,'tmf') === 0){
                $file_dir = self::$tmfPath.'/';
                break;
            }
            $file_dir = self::$appPath.'/';
        }while(false);

        // 组合路径和名称
        $file = $file_dir.$file_name . '.php';

        //将斜杠'\'换成斜杠'/'；
        if(false !== strpos($file,'\\')){
            $file = str_replace('\\','/',$file);
        }
        // 加载文件
        if (is_file($file)) {
            require($file);
        }
    }

    /**
     * 初始化框架
     */
    private  static function initTmf(){
        self::$tmfVersion = '1.0.0';
        self::$tmfPath = dirname(dirname( __FILE__));
        self::$appPath = '';

        spl_autoload_register('self::autoLoadClass');
    }

    /**
     * 创建一个web应用对象
     * @param $config_path string 配置文件相对路径
     * @return Application application web应用对象
     */
    public static function createWebAppByConfig($config_path){
        self::initTmf();
        return new Application($config_path);
    }
}