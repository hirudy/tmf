<?php
/**
 * Created by TXM.
 * Time: 15-1-23 上午10:59
 * function:
 */

namespace tmf\web\urlManager;


use tmf\web\App;

class SimplifyUrl extends UrlManager{

    public function __construct(){
        $this->analyseUrl();
    }
    private function createQueryString($controller,$action,$data = null){
        $str_query = '/'.$controller.'/'.$action;
        if(is_array($data) && !empty($data)){
            $lastParams = array();
            foreach($data as $key=>$value){
                if(!is_numeric($key))
                    $str_query .= '/'.$key.'/'.$value;
                else{
                    array_push($lastParams,$value);
                }
            }

            if(!empty($lastParams)){
                $str_query .= '/'.implode('-',$lastParams);
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
        //如果是奇数个参数 设置最后一个参数为$_GET['params'];
        if(!$is_key){
            $_GET['params'] = $key_name;
        }
    }

    //对url进行格式化
    private function formatUrl(){
        $url = str_replace(App::$http_request->getBaseURL(),'',App::$http_request->getRequestUri());
        $pos = strpos($url,'?');
        if($pos !== false){
            $url = substr($url,0,$pos);
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
            if($url_length <= 0){   //没有参数，指向默认控制器和行为
                App::$controller = App::$default_controller;
                App::$action = '';
                break;
            }

            if($url_length == 1){   //1个参数，指向默认控制器的当前行为
                App::$controller = App::$default_controller;
                App::$action = $url_arr[0];
                break;
            }
                                   //2个参数及以上，设置控制器和行为
            App::$controller = array_shift($url_arr);
            App::$action = array_shift($url_arr);
                                    //获取get参数
            $this->analyseGet($url_arr);
        }while(false);
    }
} 