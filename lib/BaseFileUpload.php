<?php
/**
 * Created by TXM.
 * Time: 15-2-9 下午12:58
 * function:
 */

namespace tmf\lib;


class BaseFileUpload {
    protected  $filename;       //上传时的文件名称（<input> 中的name属性）
    protected  $typeList;       //允许上传文件类型MIME(字符/数组)
    protected  $maxSize;        //允许上传的文件大小，单位：MB
    public     $error;          //验证文件时，失败的原因
    public     $path;           //保存文件的路径
    //构造函数，生成基本的文件上传类
    public function __construct($name,$typeList=null,$size=null){
        $this->filename = is_string($name)?$name:'';
        if(is_string($typeList)){
            $this->typeList = array($typeList);
        }elseif(is_array($typeList)){
            $this->typeList = $typeList;
        }
        $this->maxSize = is_numeric($size)?($size*1024*1024):0;
        $this->error='';
        $this->path ='';
    }

    //验证文件是否上传成功
    public function validate(){
        if(!isset($_FILES[$this->filename])){
            $this->error = 'no_file';
            return false;
        }

        $file =$_FILES[$this->filename];

        //没有上传错误
        if(!empty($file['error']) || $file['error'] > 0){
            $this->error = isset($file['error'])?$file['error']:'error';
            return false;
        }
        //符合规定的文件MIME;
        if(!empty($this->typeList) && !in_array($file['type'],$this->typeList)){
            $this->error = 'forbidden_file_type';
            return false;
        }
        //符合大小
        if(!empty($this->maxSize) && ($file['size'] > $this->maxSize)){
            $this->error = 'over_max_size';
            return false;
        }
        return true;
    }

    //通过名字保存文件
    public function saveByName($dir,$name){
        $tmp_name = $_FILES[$this->filename]['tmp_name'];
        $path = $dir.'/'.$name;
        $this->path= $path;
        return move_uploaded_file($_FILES[$this->filename]['tmp_name'],$dir.'/'.$name);
    }
} 