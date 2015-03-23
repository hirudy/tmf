<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-7
 * Time: 下午8:14
 */
namespace tmf\web\urlManager;

use tmf\web\App;
class NormalUrl extends UrlManager{

    public function __construct(){
        $this->analyseUrl();
    }
    private function createQueryString($controller,$action,$data = null){
        if(empty($controller) || empty($action))  //控制器，行为不能为空。
            return null;
        $str_query = '?r='.$controller.'/'.$action;
        if(is_array($data) && count($data)> 0)
            $str_query .= '&'.http_build_query($data);
        return $str_query;
    }
    public function createUrl($controller,$action,$data = null){
        $url =App::$http_request->getScriptURL();
        return  $url.$this->createQueryString($controller,$action,$data);
    }
    public function createAbsoluteUrl($controller,$action,$data = null){
        $url  = App::$http_request->getHostURL();
        $url .= App::$http_request->getScriptURL();
        return  $url.$this->createQueryString($controller,$action,$data);
    }
    public function analyseUrl($url = null){
        if(isset($_GET['r']) && !empty($_GET['r'])){
            $ca = $_GET['r'];
            $pos = stripos($ca,'/');
            if($pos === false){
                App::$controller = $ca;
                App::$action = '';
            }else{
                App::$controller = substr($ca,0,$pos);
                App::$action = substr($ca,$pos+1);
            }
        }else{
            App::$controller = App::$default_controller;
            App::$action = '';
        }
    }
}