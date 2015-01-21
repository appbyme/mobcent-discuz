<?php

/** 
 * 微信绑定model类
 *
 * @author HanPengyu
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymeConnection extends DiscuzAR {

    const WECHAT_BIND = 1;  // 微信绑定登录
    const WECHAT_TYPE = 1;  // 微信类型

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{appbyme_connection}}';
    }

    public function rules() {
        return array(
        );
    }

    // WX 自定义表
    public static function getMobcentWxinfoByOpenId($openId) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE openid=%s
            AND status=%n
            AND type=%n
            ',
            array('appbyme_connection', $openId, self::WECHAT_BIND, self::WECHAT_TYPE)
        );
    }

    // 插件自定义WX表
    public static function insertMobcentWx($data) {
        return DbUtils::getDzDbUtils(true)->insert('appbyme_connection', $data);
    }

    // WX 3.2微信表
    public static function getWXinfoByOpenId($openId) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE openid=%s
            AND status=%n
            ',
            array('common_member_wechatmp', $openId, 1)
        );
    }

    // 检测是否有微信登录插件
    public static function isWechat($identifier='wechat') {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE identifier=%s
            ',
            array('common_plugin', $identifier)
        );
    }
}