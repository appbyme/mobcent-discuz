<?php

/**
 * 安米用户model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymeUserAccess extends DiscuzAR {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{appbyme_user_access}}';
	}

	public function rules() {
		return array(
		);
	}

	// public function attributeLabels() {
	// 	return array(
	// 	);
	// }

    public static function getUserIdByAccess($accessToken, $accessSecret) {
        return (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT user_id
            FROM %t
            WHERE user_access_token=%s AND user_access_secret=%s
            ', 
            array('appbyme_user_access', $accessToken, $accessSecret)
        );
    }

    // auth:han
    public static function getInfoByUid($uid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE user_id=%d
            ',
            array('appbyme_user_access', $uid)
        );
    }

    public static function insertUserAccess($data) {
        return DbUtils::getDzDbUtils(true)->insert('appbyme_user_access', $data);
    }

    public static function updateUserAccess($data, $userId) {
        return DbUtils::getDzDbUtils(true)->update('appbyme_user_access', $data, array('user_id'=> $userId));
    }

    public static function getSecretStr($uid, $password) {
        return array(
            'accessToken' => substr(md5($uid.'mobcent'),0,-3),
            'accessSecret' => substr(md5($password.'mobcent'),0,-3)
        );
    }

    public static function loginProcess($uid, $password) {
        $userAccess = self::getInfoByUid($uid);
        if (!empty($userAccess)) {
            $accessToken = $userAccess['user_access_token'];
            $accessSecret =$userAccess['user_access_secret'];
        } else {
            $secretStr = self::getSecretStr($uid, $password);
            $accessToken = $secretStr['accessToken'];
            $accessSecret = $secretStr['accessSecret'];
            $data = array(
                    'user_access_id' => '',
                    'user_access_token' => $accessToken,
                    'user_access_secret' => $accessSecret,
                    'user_id' => $uid,
                    'create_time' => time()
                );
            self::insertUserAccess($data);
        }
        $res = array();
        $res['token'] = (string)$accessToken;
        $res['secret'] = (string)$accessSecret;
        return $res;        
    }

    public static function registerProcess($uid, $password) {
        $secretStr = self::getSecretStr($uid, $password);
        $accessToken = $secretStr['accessToken'];
        $accessSecret = $secretStr['accessSecret'];        
        $userAccess = self::getUserIdByAccess($accessToken, $accessSecret);
        if (!$userAccess['uid']) {
            $data = array(
                'user_access_id' => '',
                'user_access_token' => $accessToken,
                'user_access_secret' => $accessSecret,
                'user_id' => $uid,
                'create_time' => time()
            );
            self::insertUserAccess($data);
        }
        $res = array();
        $res['token'] = (string)$accessToken;
        $res['secret'] = (string)$accessSecret;
        return $res;           
    }   

}