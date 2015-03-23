<?php
/**
 * Created by TXM.
 * Time: 15-2-9 下午2:53
 * function:
 */

namespace tmf\lib;


class BaseTool {
    //获取$length长度的随机字符
    public static function getRandString($length){
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        return substr(str_shuffle($str),0,$length);
    }
    //获取随机文件名
    public static function getRandShortString(){
        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $name = substr(str_shuffle($str),0,10);
        return $name;
    }

    //创建一个目录(年-月)
    public static function getDateDir($root_dir){
        //保证根目录存在
        if(!is_dir($root_dir)) {
            if(!mkdir($root_dir)) {
                return false;
            }
        }
        //获取子目录名
        $sub_dir = date('Ym');
        $dir = $root_dir.$sub_dir;
        //创建子目录
        if(!is_dir($dir)) {
            if(!mkdir($dir)) {
                return false;
            }
        }
        return $sub_dir;
    }
} 