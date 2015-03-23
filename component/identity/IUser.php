<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-8
 * Time: 下午5:51
 */
namespace tmf\component\identity;

interface IUser{
    public function login(Identity $identity);        //登陆
    public function logout();                         //退出
    public function IsLogin();                        //是否是登陆状态
    public function loginFromCookie();                //通过cookie进行登陆
    public function hasSession($key);                 //判断$key是否在session中
    public function getSession($key);                 //获取$key在session中的值
    public function setSession($key,$value);          //设置$key在session中值
}