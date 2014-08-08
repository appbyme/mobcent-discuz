<?php


/**
 * 数据库缓存依赖类, 为了解决Yii底层应用了PDO的情况
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

class DiscuzDbCacheDependency extends CCacheDependency {

    public $reuseDependentData = true;
    
    public $sql;
    public $params;

    public function __construct($sql, $params) {
        $this->sql = $sql;
        $this->params = $params;
    }

    protected function generateDependentData() {
        if ($this->sql != '') {
            return DbUtils::getDzDbUtils(true)->queryRow($this->sql, $this->params);
        }
        else {
            throw new CException(Yii::t('yii','CDbCacheDependency.sql cannot be empty.'));
        }
    }
}