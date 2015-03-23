<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-8
 * Time: 下午12:18
 */
namespace tmf\db\mysql;

use tmf\db\Db;
use tmf\web\App;

class mysqlDb extends Db{
    private $error;                //错误信息
    private static $pdo;           //数据库PDO

    /**
     * 获取一个PDO连接对象
     * @return PDO pdo对象
     */
    public function getPdoIns(){
        if(self::$pdo == null){
            $con = App::$app_config['db']['one'];
            $pdo = new \PDO($con['connectionString'],$con['username'],$con['password']);
            $pdo->query('set names '.$con['charset']);
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    /**
     * 根据$sql返回结果集
     * @param $sql
     * @param null $params 参数
     * @return array|bool false-出错，空数组-无值，数组-结果集
     */
    public function query($sql,$params=null){
        $pdo = $this->getPdoIns();
        $stmt = null;
        if($params == null || !is_array($params)){//无参数时
            $stmt = $pdo->query($sql);
            if($stmt === false){
                $this->error = $pdo->errorInfo();
                return false;
            }
        }else{                                  //有参数时
            $stmt = $pdo->prepare($sql);
            if($stmt === false || !$stmt->execute($params)){
                $this->error = $stmt->errorInfo();
                return false;
            }
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC );
    }

    /**
     * 通过$sql返回受影响的行数（不能执行select）
     * @param $sql
     * @param null $params
     * @return bool|int false-出错 数字-受影响的行数（可为0）
     */
    public function exec($sql,$params=null){
        $pdo = $this->getPdoIns();
        $rel = null;
        if($params == null || !is_array($params)){//无参数时
            $rel = $pdo->exec($sql);
            if($rel === false){
                $this->error = $pdo->errorInfo();
                return false;
            }
            return $rel;
        }else{                                  //有参数时
            $stmt = $pdo->prepare($sql);
            if($stmt === false || !$stmt->execute($params)){
                $this->error = $stmt->errorInfo();
                return false;
            }
            return $stmt->rowCount();
        }
    }

    /**
     * 删除相应东西
     * @param $table string 要删除的表
     * @param null $where array 删除条件
     * @return bool|int 同exec()
     */
    public function delete($table,$where=null){
        $sql = 'delete from '.$table;
        if($where != null && is_array($where)){
            $where_str = implode('=? ',array_keys($where)).'=?';
            $sql .= ' where '.$where_str;
            $where = array_values($where);
        }
        return $this->exec($sql,$where);
    }

    /**
     * 添加或修改，当有主键值时为修改，当无主键值时为添加。
     * @param $table string 表名
     * @param $datas array 保存数据（键名-键值）
     * @param null $pk_name 主键名称
     * @param null $pk_value 主键值
     * @return bool|int 同exec()
     */
    public function save($table,$datas,$pk_name=null,$pk_value=null){
        if($datas == null || !is_array($datas))
            return false;
        $keys = array_keys($datas);
        $values = array_values($datas);

        if($pk_name == null || $pk_value == null){//插入
            $sql = 'insert into '.$table.' (%s) values (%s)';
            $str_key = implode(',',$keys);
            $occ_str = implode(',',array_fill(0, count($datas),'?'));
            $sql = sprintf($sql,$str_key,$occ_str);
        }else{//更改
            $update_sql = 'update ' . $table . ' set %s where ' . $pk_name . ' = ?';
            $occ_str = implode("=?,", $keys) . "=?";
            $sql = sprintf($update_sql, $occ_str);
            $values[] = $pk_value;
        }
        return $this->exec($sql,$values);
    }
    public function getError(){
        return $this->error;
    }
    public function getLastInsertId($pk_name=null){
        return $this->getPdoIns()->lastInsertId($pk_name);
    }
}