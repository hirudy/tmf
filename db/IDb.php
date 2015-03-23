<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-8
 * Time: 下午12:02
 */
namespace tmf\db;

interface IDb{
    public function getPdoIns();    //通过配置文件获取Pdo实例对象
    public function query($sql,$params= null);  //查询 返回结果集
    public function exec($sql,$params= null);   //查询 返回受影响的行数
    public function delete($table,$where);//删除
    public function save($table,$data,$pk_name=null,$pk_value=null); //保存或添加
    public function getError();     //获取错误信息
    public function getLastInsertId($pk_name=null);//返回最后插入行的ID或序列值;
}