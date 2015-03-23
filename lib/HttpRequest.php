<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-7
 * Time: 下午4:48
 */
namespace tmf\lib;

//http请求头部分析
//use tmf\web\Application;

class HttpRequest{
    private $_baseUrl;      //相对基本路径
    private $_requestUri;  //请求地址（不包含如http://主机名）
    private $_userIp;       //访问者IP地址；
    private $_scriptFile;  //请求脚本绝对文件路径
    private $_scriptUrl;   //脚本相对URL
    private $_port;         //服务器端口号
    private $_hostURL;    //请求服务器URL(如：http://主机名)
    private $_isHttps;     //是否为https请求

    //获取相对Uri (如：/test/)
    public function getRequestUri(){
        if($this->_requestUri == null) //Apache
        {
            $this->_requestUri = $_SERVER['REQUEST_URI'];
            if(empty($_SERVER['HTTP_HOST']))
            {   //主机名为空
                $this->_requestUri=preg_replace('/^(http|https):\/\/[^\/]+/i','',$this->_requestUri);
            }
            else
            {
                if(strpos($this->_requestUri,$_SERVER['HTTP_HOST'])!==false)
                    $this->_requestUri=preg_replace('/^\w+:\/\/[^\/]+/','',$this->_requestUri);
            }
        }
        return $this->_requestUri;
    }

    //返回主机名称（如：localhost）
    public function getHostName(){
        return $_SERVER['HTTP_HOST'];
    }
    //返回链接/提交当前页的父页面URL.
    public function getReferrerURL(){
        return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
    }
    //获取客户端信息
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
    }
    //获取访问者的IP地址
    public function getUserIp(){
        if($this->_userIp == null){
            do{
                //check ip from share internet
                if (!empty($_SERVER['HTTP_CLIENT_IP'])){
                    $ip=$_SERVER['HTTP_CLIENT_IP'];
                    break;
                }
                //to check ip is pass from proxy
                if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                    break;
                }
                $ip=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:null;
            }while(false);
            $this->_userIp = $ip;
        }
        return $this->_userIp;
    }
    //获取请求脚本的绝对文件路径（如：C:\www\test\index.php）
    public function getAbsoluteScriptFile()
    {
        if($this->_scriptFile!==null)
            return $this->_scriptFile;
        else
            return $this->_scriptFile=realpath($_SERVER['SCRIPT_FILENAME']);
    }
    //获取应用根目录（如：C:\www\test）
    public function getAbsoluteBasePath(){
        return dirname($this->getAbsoluteScriptFile());
    }
    //获取请求脚本相对URL（如：/test/index.php）
    public function getScriptURL()
    {
        if($this->_scriptUrl===null)
        {
            $scriptName=basename($_SERVER['SCRIPT_FILENAME']);
            if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
                $this->_scriptUrl=$_SERVER['SCRIPT_NAME'];
            else if(basename($_SERVER['PHP_SELF'])===$scriptName)
                $this->_scriptUrl=$_SERVER['PHP_SELF'];
            else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
                $this->_scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
            else if(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
                $this->_scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
            else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
                $this->_scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
        }
        return $this->_scriptUrl;
    }

    //获取服务器端，端口号
    public function getServerPort()
    {
        $isHttps = $this->isHttps();
        $defaultValue = $isHttps?443:80;
        if($this->_port===null)
            $this->_port= isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] :$defaultValue;
        return $this->_port;
    }
    //判断是否为HTTPS请求
    public function isHttps(){
        if($this->_isHttps === null)
            $this->_isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        return $this->_isHttps;
    }

    /**
     * 获取主机url（如：http://localhost）
     * @param string $schema 将要改变的策略（http/https）
     * @return string 主机URL
     */
    public function getHostURL($schema=null)
    {
        if($this->_hostURL == null)
        {   //_hostURL 没有被初始化，初始化他
            $http = 'http';//是何种协议
            if($this->isHttps())
            {
                $http = 'https';
            }
            //_hostURL 初始化
            if(isset($_SERVER['HTTP_HOST']))
                $this->_hostURL = $http.'://'.$_SERVER['HTTP_HOST'];
            else{
                $port= $this->getServerPort();
                if(!($port === 80 && $http == 'http') || !($port === 433 && $http == 'https'))
                    $this->_hostURL.=':'.$port;
            }
        }
        //改变策略（http/https）
        if($schema!==null && ($schema == 'http'|| $schema == 'https')){
            $secure=$this->isHttps();
            //schema是否与当前策略相等，相等直接返回
            if($secure == $schema)
                return $this->_hostURL;

            $port= $this->getServerPort();
            if(($port!==80 && $schema==='http') || ($port!==443 && $schema==='https'))
                $port=':'.$port;
            else
                $port='';
            $pos=strpos($this->_hostURL,':');
            $url = $schema.substr($this->_hostURL,$pos,strcspn($this->_hostURL,':',$pos+1)+1).$port;
            return $url;
        }
        return $this->_hostURL;
    }
    //获取相对路径URL（如：/test）
    public function getBaseURL($absolute=false)
    {
        if($this->_baseUrl===null)
            $this->_baseUrl=rtrim(dirname($this->getScriptURL()),'\\/');
        return $absolute ? ($this->getHostURL() . $this->_baseUrl) : $this->_baseUrl;
    }
    //获取绝对路径URL（如：http://localhost/test）
    public function getAbsoluteBaseUrl()
    {
        return $this->getBaseUrl(true);
    }
}