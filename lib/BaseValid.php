<?php
/**
 * Created by TXM.
 * Time: 15-2-2 上午8:46
 * function:
 */

namespace tmf\lib;


class BaseValid {

    //不为空
    public static function required($var){
        return empty($var)?false:$var;
    }

    //输入字符串为email
    public static function email($var){
        return filter_var($var, FILTER_VALIDATE_EMAIL);
    }

    //输入字符串是一个url
    public static function url($var,$params=null){
        return filter_var($var,FILTER_VALIDATE_URL,$params);
    }

    //是一个boolean值
    public static function boolean($var){
        return is_bool($var)?$var:false;
    }
    /***************改变输入内容******************/
    //从字符串中去除 HTML 和 PHP 标记
    public static function noScriptTags($str){
        if(is_string($str)){
            return strip_tags($str);
        }
        return '';
    }

    //将html进行转换
    public static function htmlSpecialChars($str){
        if(is_string($str)){
            return htmlspecialchars($str);
        }
        return '';
    }

    /**************字符串*************************/
    //是一个字符串
    public static function string($var){
        return is_string($var)?$var:false;
    }

    //是一个字符串，长度大于等于mix
    public static function minLength($var,$min){
        if(is_string($var) && is_int($min)){
            return mb_strlen($var) >= $min?$var:false;
        }
        return false;
    }

    //是一个字符串，长度小于等于max
    public static function maxLength($var,$max){
        if(is_string($var) && is_int($max)){
            return mb_strlen($var) <= $max?$var:false;
        }
        return false;
    }

    //是一个字符串，长度小于等于max,大于等于min
    public static function rangeLength($var,$param){
        $min = isset($param[0])?$param[0]:0;
        $max = isset($param[1])?$param[1]:0;
        if(is_string($var) && is_int($min) && is_int($max)){
            $length = mb_strlen($var);
            if($length <= $max && $length >= $min)
                return $var;
            else
                return false;
        }
        return false;
    }

    /**************整数字*************************/
    //是一个整数
    public static function int($var){
        return is_int($var)?$var:false;
    }

    //是一个整数，值大于等于min
    public static function intMin($var,$min){
        if(is_int($var) && is_int($min)){
            return $var >= $min?$var:false;
        }
        return false;
    }

    //是一个整数，值小于等于max
    public static function intMax($var,$max){
        if(is_int($var) && is_int($max)){
            return $var <= $max?$var:false;
        }
        return false;
    }

    //是一个整数（范围（min,max））
    public static function intRange($var,$param){
        $min = isset($param[0])?$param[0]:0;
        $max = isset($param[1])?$param[1]:0;
        return filter_var($var,FILTER_VALIDATE_INT,array("options"=>array("min_range"=>$min, "max_range"=>$max)));
    }

    /**************数字*************************/
    //是一个数
    public static function number($var){
        return is_numeric($var)?$var:false;
    }
    //是一个数，值小于等于max
    public static function Max($var,$max){
        if(is_numeric($var) && is_numeric($max)){
            return ($var <= $max)?$var:false;
        }
        return false;
    }
    //是一个数，值大于等于min
    public static function Min($var,$min=0){
        if(is_numeric($var) && is_numeric($min)){
            return ($var >= $min)?$var:false;
        }
        return false;
    }
    //是一个数（范围（min,max））
    public static function Range($var,$param){
        $min = isset($param[0])?$param[0]:0;
        $max = isset($param[1])?$param[1]:0;
        return filter_var($var,FILTER_VALIDATE_INT,array("options"=>array("min_range"=>$min, "max_range"=>$max)));
    }


    //通过正则表达式验证
    public static function regexp($var,$string){
        if(!empty($string) && is_string($string))
            return filter_var($var, FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>$string)));
        return $var;
    }

    //在数组arr中,是否存在键名为name的键值对。
    public static function nameInArr($name,$arr){
        if(empty($arr) || empty($name) || !is_array($arr))
            return false;
        if(is_string($name)){//是字符串
            return array_key_exists($name,$arr)?$name:false;
        }else if(is_array($name)){//是数组（多个名字）
            $rel = false;
            foreach($name as $value){
                $rel = array_key_exists($value,$arr);
                if($rel == false)
                    break;
            }
            return $rel?$name:false;
        }
        return false;
    }
    //一个变量，多次验证
    public static function validate($var,$params){
        $result = $var;
        foreach($params as $key => $value){
            if(is_numeric($key)){//无参数
                $result = self::$value($result);
            }else{//有参数
                $result = self::$key($result,$value);
            }
            if($result === false)
                break;
        }
        return $result;
    }

    //多个变量，同样的多次验证
    public static function validates($arr,$params){
        if(empty($arr) || empty($params))
            return $arr;
        if(is_array($arr)){
            $rel = false;
            $rel_arr = array();
            foreach($arr as $key=>$value){
                $rel = self::validate($value,$params);
                if($rel == false)
                    break;
                else{
                    $rel_arr[$key] = $rel;
                }
            }
            return $rel == false ?false:$rel_arr;
        }
        return false;
    }
} 