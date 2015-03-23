<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-7
 * Time: 下午10:05
 */
namespace tmf\lib;
use tmf\web\App;
class ErrorTip{
    public static function Error($info,$position){
        if(App::$debug){
            $str = '<p style="color: #FF0000;"><span style="color: #FFF;background-color:#777;">ERROR&nbsp&nbsp&nbsp&nbsp&nbsp</span>&nbsp&nbsp';
            $str.= $info.'<br><span style="color:#FFF;background-color:#777;">position:</span>&nbsp&nbsp'.$position.'</p>';
            echo $str;
        }
        exit;
    }
    public static function warn($info,$position){
        if(App::$debug){
            $str = '<p style="color: #0000FF;"><span style="color: #FFF;background-color:#777;">WARN&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>&nbsp&nbsp';
            $str.= $info.'<br><span style="color:#FFF;background-color:#777;">position:</span>&nbsp&nbsp'.$position.'</p>';
            echo $str;
        }
    }
}