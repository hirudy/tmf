<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-8
 * Time: 下午4:28
 */
namespace tmf\model;

use tmf\lib\ErrorTip;
use tmf\web\App;

abstract class Model implements IModel{
    protected $db;//数据库操作对象
    protected $table;
    protected $pk_name;

    private function showError(){
        $error = $this->getError();
        ErrorTip::Error('DatabaseError:'.$error[2],__FILE__);
    }

    public function __construct(){
        $this->db = App::$db;
    }
    public function getPdo(){
        $res = $this->db->getPdoIns();
        if($res === false)
            $this->showError();
        return $res;
    }
    public function query($sql, $params = null){
        $res = $this->db->query($sql,$params);
        if($res === false)
            $this->showError();
        return $res;
    }
    public function exec($sql,$params=null){
        $res = $this->db->exec($sql,$params);
        if($res === false)
            $this->showError();
        return $res;
    }
    public function save($datas, $pk_value = null){
        $res = $this->db->save($this->table,$datas,$this->pk_name,$pk_value);
        if($res === false)
            $this->showError();
        return $res;
    }
    public function delete($where=null){
        $res = $this->db->delete($this->table,$where);
        if($res === false)
            $this->showError();
        return $res;
    }
    public function getLastInsertId($pk_name = null){
        return $this->db->getLastInsertId($pk_name);
    }
    public function getError(){
        return $this->db->getError();
    }

    public function find($sql, $param = null){
        $res = $this->query($sql, $param);
        if($res === false)
            return false;
        if(count($res) == 0)
            return array();
        return $res[0];
    }
    public function deleteByPk($pk_v){
        $key = $this->pk_name;
        return $this->delete(array($key => $pk_v));
    }
    public function findByPk($pk_v, $fields){
        $sql = sprintf("select %s from %s where %s = ? ", implode(',', $fields), $this->table, $this->pk_name);
        return $this->find($sql, array($pk_v));
    }
};