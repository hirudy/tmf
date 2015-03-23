<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-8
 * Time: 下午4:23
 */
namespace tmf\model;

interface IModel{
    public function query($sql, $params = null); //查询
    public function save($datas, $pk_value = null);//保存、添加
    public function delete($where=null);        //删除
    public function exec($sql,$params=null); //通过sql语句的执行，返回受影响的行数
    public function getLastInsertId($pk_name = null);          //获取插入ID
    public function getError();                 //获取错误信息

    public function find($sql, $param = null);  //返回一个结果
    public function deleteByPk($pk_v);          //通过主键删除
    public function findByPk($pk_v, $fields);   //通过主键查找
}