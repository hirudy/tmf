<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-8
 * Time: 下午5:54
 */
namespace tmf\component\identity;

use tmf\lib\BaseValid;
use tmf\web\App;

class WebUser implements IUser{
    const user_cookie_info = 'tmf_user_info';
    const auto_login_time = 604800; //一个周，（一周不登陆取消自动登陆）
    public function login(Identity $identity){
        do{
            if($this->IsLogin())
                break;          //已经登陆了,就不要再登陆了
            setcookie(self::user_cookie_info,null,time()-3600);//清掉旧的cookie;
            if(!$identity->authenticate())
                return false;  //验证失败,返回false;
            //写入session
            $this->setSession('sid',$identity->getId());
                                //是否自动登陆
            if($identity->autoLogin){
                $cookie_info = json_encode(array('u'=>$identity->username, 'pm'=>$identity->password));
                $path =App::$http_request->getBaseURL();
                setcookie(self::user_cookie_info, $cookie_info, time() + self::auto_login_time,$path);
            }
        }while(false);
        return true;
    }

    public function logout(){
        $path =App::$http_request->getBaseURL();
        setcookie(self::user_cookie_info,null,time()-3600,$path);
        session_destroy();
    }

    public function isLogin(){//判断$_SESSION['tmf_id']是否设置
        return $this->hasSession('sid');
    }

    public function loginFromCookie(){
        if($this->IsLogin())//已经登陆没必要执行后面的操作
            return true;
        //数据处理
        $isUIExist = BaseValid::nameInArr(self::user_cookie_info,$_COOKIE);
        $isCIExist = BaseValid::nameInArr('identity',App::$app_config);
        if(!($isUIExist && $isCIExist))
            return false;
        $user_info = json_decode($_COOKIE[self::user_cookie_info],true);
        if(!is_array($user_info) || !isset($user_info['u']) || !isset($user_info['pm']))//验证格式
            return false;
        //创建验证对象
        $str_identity = App::$app_config['identity'];
        $identity = new $str_identity($user_info['u'],$user_info['pm']);
        return $this->login($identity);
    }

    //判断是否有session
    public function hasSession($key){
        $name = 'tmf_'.$key;
        return isset($_SESSION[$name]);
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
}