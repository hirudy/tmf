<?php
/**
 * Created by TXM.
 * Time: 15-1-16 下午10:56
 * function: 格式化的url(如：controller/action/p/12.html => ?r=controller/action&p=12)
 */

namespace tmf\web\urlManager;


use tmf\web\App;

class FormatUrl extends UrlManager{

    public function __construct(){
        $this->analyseUrl();
    }
    private function createQueryString($controller,$action,$data = null){
        $str_query = '/'.$controller.'/'.$action;
        if(is_array($data) && !empty($data)){
            foreach($data as $key=>$value){
                $str_query .= '/'.$key.'/'.$value;
            }
        }
        $str_query .= '.html';
        return $str_query;
    }

    //分析get参数
    private function analyseGet($arr){
        $is_key = true;
        $key_name = '';
        foreach($arr as $value){
            if($is_key){
                $key_name = $value;
                $is_key = false;
            }else{
                $_GET[$key_name] = $value;
                $key_name = '';
                $is_key = true;
            }
        }
        if(!$is_key){
            $_GET[$key_name] = '';
        }
    }
    private function formatUrl(){
        //对url进行格式化
        $uri = str_replace(App::$http_request->getBaseURL(),'',App::$http_request->getRequestUri());
        $pos = strpos($uri,'?');
        $url = $uri;
        if($pos !== false){
            $url = substr($uri,0,$pos);
        }
        $pos = strpos($url,'.html');
        if($pos !== false){
            $url = substr($url,0,$pos);
        }

        $url = trim($url,'/');
        return explode('/',$url);
    }

    public function createUrl($controller,$action,$data = null){
        $url =App::$http_request->getBaseURL();
        return  $url.$this->createQueryString($controller,$action,$data);
    }
    public function createAbsoluteUrl($controller,$action,$data = null){
        $url  = App::$http_request->getHostURL();
        $url .= App::$http_request->getBaseURL();
        return  $url.$this->createQueryString($controller,$action,$data);
    }
    public function analyseUrl(){

        $url_arr = $this->formatUrl();

        //设置当前控制器和行为，以及get参数
        $url_length = count($url_arr);
        do{
            if($url_length <= 0){
                App::$controller = App::$default_controller;
                App::$action = '';
                break;
            }

            if($url_length == 1){
                App::$controller = App::$default_controller;
                App::$action = $url_arr[0];
                break;
            }

            App::$controller = array_shift($url_arr);
            App::$action = array_shift($url_arr);

            $this->analyseGet($url_arr);
        }while(false);
    }
}