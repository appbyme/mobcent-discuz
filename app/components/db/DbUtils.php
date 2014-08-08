<?php

/**
 * 全局数据库工具类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DbUtils {

    public static $rawDzDb = null;  // discuz 自带的 db 连接方式
    public static $mobcentDiscuzDb = null;

    public static function init($initDzDb=true) {
        self::$rawDzDb == null && self::$rawDzDb = DbUtils::createDbUtils(true);
        $dbConfig = Yii::app()->params['mobcent']['db'];
        if ($initDzDb) {
            if (self::$mobcentDiscuzDb == null) {
                self::$mobcentDiscuzDb = DbUtils::createDbUtils();
                if (!self::$mobcentDiscuzDb->init($dbConfig['discuz'])) {
                    throw new CDbException('mobcentDiscuzDb connect failed');
                }
            }
        }
    }

    public static function createDbUtils($isDiscuzRaw = false) {
        return $isDiscuzRaw ? new DiscuzDbUtils :  new MysqlDbUtils;
    }

    public static function getDzDbUtils($isDiscuzRaw = false) {
        return $isDiscuzRaw ? self::$rawDzDb : self::$mobcentDiscuzDb;
    }

    public static function getPageCommand($command, $page, $pageSize) {
        if ($page >= 1 && $pageSize > 0) {
            $command = $command->limit($pageSize, ($page-1)*$pageSize);
        }
        return $command;
    }
}

class DiscuzDbUtils {

    public function query($sql='', $params=array()) {
        return DB::query($sql, $params);
    }

    public function queryAll($sql='', $params=array()) {
        return DB::fetch_all($sql, $params);
    }

    public function queryRow($sql='', $params=array()) {
        return DB::fetch_first($sql, $params);
    }

    public function queryScalar($sql='', $params=array()) {
        $row = $this->queryRow($sql, $params);
        return !empty($row) ? current($row) : false;
    }

    public function queryColumn($sql='', $params=array()) {
        $res = array();
        $rows= $this->queryAll($sql, $params);
        foreach ($rows as $row) {
            $res[] = current($row);
        }
        return $res;
    }

    public function insert($tableName, $data) {
        return DB::insert($tableName, $data);
    }

    public function update($tableName, $data, $condition) {
        return DB::update($tableName, $data, $condition);
    }

    public function delete($tableName, $condition) {
        return DB::delete($tableName, $condition);
    }

    public function save($tableName, $data, $condition) {

    }

    public function dumpDebug() {
        foreach (DB::$db->sqldebug as $debug) {
            var_dump($debug[0]);
            var_dump($debug[1]);
        }
    }
}