<?php

/**
 * 数据库底层基类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

abstract class XJP_DbUtils {
    
    public $server = 'localhost';
    public $port = 3306;
    public $username = '';
    public $password = '';
    
    public $dbName = '';
    public $charset = 'utf8';
    public $tablePrefix = '';

    public $sql = '';

    public function init($config) {
        isset($config['server']) && $this->server = $config['server'];
        isset($config['port']) && $this->port = $config['port'];
        isset($config['username']) && $this->username = $config['username'];
        isset($config['password']) && $this->password = $config['password'];
        isset($config['dbName']) && $this->dbName = $config['dbName'];
        isset($config['charset']) && $this->charset = $config['charset'];
        isset($config['tablePrefix']) && $this->tablePrefix = $config['tablePrefix'];

        return $this->connect();
    }

    abstract protected function connect();

    abstract public function query($sql='', $params=array());
    abstract public function queryAll($sql='', $params=array());
    abstract public function queryRow($sql='', $params=array());
    abstract public function queryScalar($sql='', $params=array());
    abstract public function queryColumn($sql='', $params=array());
}

/**
 * mysql 数据库实现类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 */

class MysqlDbUtils extends XJP_DbUtils {

    public $link = null;

    protected function connect() {
        if ($link = mysql_connect($this->server.':'.$this->port, $this->username, $this->password, true)) {
            if (mysql_select_db($this->dbName, $link) && $this->_setCharset($this->charset, $link)) {
                $this->link = $link;
            }
        }
        return is_resource($this->link);
    }

    public function setSql($sql, $params=array()) {
        $this->sql = preg_replace('/{{(.+?)}}/', $this->tablePrefix.'$1', $sql);
        // var_dump($this->sql);
        return $this;
    }

    public function getError() {
        return mysql_error($this->link);
    }

    public function query($sql='', $params=array()) {
        $sql != '' && $this->setSql($sql, $params);
        return $this->_query();
    }

    public function queryAll($sql='', $params=array()) {
        $rows = array();
        if (($result = $this->query($sql, $params)) !== false) {
            while (($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false) {
                $rows[] = $row;
            }
            mysql_free_result($result);
        }
        return $rows;
    }

    public function queryRow($sql='', $params=array()) {
        $row = array();
        if (($result = $this->query($sql, $params)) !== false) {
            ($tempRow = mysql_fetch_array($result, MYSQL_ASSOC)) !== false && $row = $tempRow;
            mysql_free_result($result);
        }
        return $row;
    }

    public function queryScalar($sql='', $params=array()) {
        $row = $this->queryRow($sql, $params);
        return !empty($row) ? current($row) : false;
    }

    public function queryColumn($sql='', $params=array()) {
        $rows = array();
        if (($result = $this->query($sql, $params)) !== false) {
            while (($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false) {
                $rows[] = current($row);
            }
            mysql_free_result($result);
        }
        return $rows;
    }

    private function _query() {
        $res = mysql_query($this->sql, $this->link);
        $this->sql = '';
        return $res;
    }

    private function _setCharset($charset, $link) {
        // character_set_database='{$charset}', 
        // character_set_server='{$charset}'
        $query = "SET character_set_results='{$charset}', character_set_client='{$charset}', 
            character_set_connection='{$charset}'";
        return mysql_query($query, $link);
    }

}