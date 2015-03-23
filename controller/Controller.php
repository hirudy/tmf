<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-9
 * Time: 下午9:29
 */
namespace tmf\controller;

use tmf\web\App;
use tmf\web\Application;

class Controller extends AController{

    private function renderView($file_path,$data=null,$return=false){
        if(is_array($data)){
            extract($data,EXTR_PREFIX_SAME,'tmf');
            unset($data);
        }

        if($return){
            ob_start();
            ob_implicit_flush(false);
            require($file_path);
            return ob_get_clean();
        }
        require($file_path);
        return true;
    }

    private function analyseConfigFilter(){
        $res = isset(App::$app_config['filter'])?App::$app_config['filter']:array();
        if(!empty($res) && is_array($res)){
            $temp =array();
            $str_controller = strtolower(App::$controller);
            foreach($res as $key=>$value){
                if(strtolower($key) == $str_controller)
                    $temp = $value;
            }
            if(isset($res['*']))
                $res =$res['*'];
            if(!empty($key))
                $res = array_merge($res,$temp);
        }
        return $res;
    }

    public function getCurrentActionFilterName(){
        $arr_filter = array();
        $configFilter = $this->analyseConfigFilter();
        $controllerFilter = $this->filter;
        $filters = array_merge($configFilter,$controllerFilter);
        foreach($filters as $key=>$value){
            if(is_string($value)){
                if($value == '*')
                    $arr_filter[] = $key;
                else{
                    $pos = stripos($value,App::$action);
                    if($pos !== false)
                        $arr_filter[] = $key;
                }
            }
        }
        return $arr_filter;
    }

    public function createUrl($base_info,$data = null,$absolute=false){
        if(!is_string($base_info) || empty($base_info)) //是字符串，且必须不能为空
            return false;

        $pos = stripos($base_info,'/');
        if(!$pos)                                        //必须包含控制器和行为
            return false;
        $controller = substr($base_info,0,$pos);
        $action     = substr($base_info,$pos+1);
        if($absolute)
            return App::$url_manager->createAbsoluteUrl($controller,$action,$data);
        return App::$url_manager->createUrl($controller,$action,$data);
    }

    public function createAbsoluteUrl($base_info,$data=null){
        return $this->createUrl($base_info,$data,true);
    }

    public function beforeAction(){return true;}

    public function render($base_info,$data=null,$return=false){
        if(!is_string($base_info) || empty($base_info)) //是字符串，且必须不能为空
            return false;
        //获取控制器和视图文件名称
        $pos = stripos($base_info,'/');
        if($pos === false){
            $controller = App::$controller;
            $action = $base_info;
        }else{
            $controller = substr($base_info,0,$pos);
            $action = substr($base_info,$pos+1);
        }
        //对视图，布局文件进行渲染
        $view_path = App::getProtectedDir().'/views/'.$controller.'/'.$action.($this->view_suffix);
        if($this->layout == false){
            $result = $this->renderView($view_path,$data,$return);
        }else{
            $layout_path = App::getProtectedDir().'/views/layout/'.($this->layout).($this->view_suffix);
            $content = $this->renderView($view_path,$data,true);
            $result = $this->renderView($layout_path,array('content'=>$content),$return);
        }
        return $result;
    }

    public function forward($base_info,$data=null){
        if(!is_string($base_info) || empty($base_info)) //是字符串，且必须不能为空
            return false;
        //获取控制器和视图文件名称
        $pos = stripos($base_info,'/');
        if($pos === false){
            App::$action = $base_info;
        }else{
            App::$controller = substr($base_info,0,$pos);
            App::$action = substr($base_info,$pos+1);
        }
        App::$dispatcher->dispatch();
        return true;
    }

    public function redirectByUrl($url,$terminate=true, $statusCode=302){
        header('Location: ' . $url, true, $statusCode);
        if($terminate) {
            Application::end();
        }
    }

    public function redirect($base_info,$data=null,$absolute = true, $terminate = true, $statusCode = 302){
        if($absolute)
            $url = $this->createAbsoluteUrl($base_info, $data);
        else {
            $url = $this->createUrl($base_info, $data);
        }
        $this->redirectByUrl($url,$terminate,$statusCode);
    }
}