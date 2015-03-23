<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-9
 * Time: 下午3:33
 */
namespace tmf\component\identity;

abstract class Identity{
    protected $username;    //登陆验证名称
    protected $password;    //为加密后的密码
    protected $autoLogin;   //是否自动登陆
    public function __get($name){
        return $this->$name;
    }
    public function __construct($username,$password,$autoLogin = true){
        $this->username = $username;
        $this->password = $password;
        $this->autoLogin = $autoLogin;
    }
    //获取$key在session中的值
    public function getSession($key){
        $name = 'tmf_'.$key;
        return isset($_SESSION[$name])?$_SESSION[$name]:null;
    }
    //设置$key在session中值
    public function setSession($key,$value){
        $name = 'tmf_'.$key;
        $_SESSION[$name] = $value;
    }

    abstract public function authenticate(); //返回boolean,false-验证不通过，true-验证通过
    abstract public function getId();        //返回用户唯一标识符
}