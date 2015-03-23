<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-9
 * Time: 下午9:18
 */
namespace tmf\web\dispatcher;

use tmf\controller\Controller;
use tmf\lib\ErrorTip;
use tmf\web\App;
use tmf\web\Application;

class NormalDispatcher extends Dispatcher{
    protected function setDefaultAction(){
        App::$default_action = $this->controller->defaultAction;
        //如果当前$action为空，使用控制器中默认行为
        if(empty(App::$action))
            App::$action = App::$default_action;
    }
    protected function createController(){
        $str_controller = App::$controller;
        $str_controller = App::$protected_name.'\\controllers\\'.$str_controller.'Controller';
        $reflect_class  = null;
        try{
            $reflect_class = new \ReflectionClass($str_controller);
        }catch (\Exception $e){
            $this->toError();
        }

        if($reflect_class->isSubclassOf('tmf\controller\AController'))
            return $reflect_class->newInstanceArgs();
        else{
            ErrorTip::Error('当前控制器不是tmf\controller\AController的子类',__FILE__);
            return null;
        }
    }

    protected function doAction(){
        $action = 'action'.App::$action;
        $exist = method_exists($this->controller,$action);

        if($exist)
            $this->controller->$action();
        else{
            $this->toError();
        }
    }

    protected function isFilter(){
        $arr_filter = $this->controller->getCurrentActionFilterName();
        if(empty($arr_filter))
            return true;

        $res = true;
        foreach($arr_filter as $value){
            $filter = new $value();
            $pass = $filter->isPass();
            if(!$pass){
                $this->filter = $filter;
                $res = false;
                break;
            }
        }
        return $res;
    }

    public function dispatch(){
        $controller = $this->createController();
        $this->controller = $controller;
        $this->setDefaultAction();
        if($this->isFilter()){
            if($controller->beforeAction())
                $this->doAction();
        }else{
            $this->filter->errorActon($controller);
        }
    }

    public function toError(){
        if(empty(App::$errorConAct)){
            echo 'error!';
        }else{
            $pos = strpos(App::$errorConAct,'/');
            App::$controller = substr(App::$errorConAct,0,$pos);
            App::$action = substr(App::$errorConAct,$pos+1);
            $this->dispatch();
        }
        Application::end();
    }
}