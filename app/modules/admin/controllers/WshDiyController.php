<?php

/**
 * 微生活
 *
 * @author HanPengyu
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class WshDiyController extends AdminController{

    public function actionIndex() {
        $moduleList = AppbymeWshDiyModel::allModule();
        $this->renderPartial('index', array('moduleList' => $moduleList));
    }

    public function actionAddPublic() {

        $res = WebUtils::initWebApiResult();
        $data = self::filterPost();
        $result = AppbymeWshDiyModel::insertModule($data); 

        if ($result) {
            $res['errCode'] = 1;
        } else {
            $res['errCode'] = 0;
        }
        echo WebUtils::outputWebApi($res, '', false);   
    }

    public function actionEditPublic($id) {

        $res = WebUtils::initWebApiResult();
        if (empty($_POST)) {
            $moduleRow = AppbymeWshDiyModel::getModuleById($id);
            $res['moduleRow'] = $moduleRow;
            echo WebUtils::outputWebApi($res, '', false);   
        } else {
            $data = self::filterPost();
            AppbymeWshDiyModel::updateModule($id, $data);
            $res['errCode'] = 1;
            echo WebUtils::outputWebApi($res, '', false);   
        }
    }
    
    public function actionDelPublic($id) {
        AppbymeWshDiyModel::delModule($id);
        $url = Yii::app()->createAbsoluteUrl('admin/wshdiy/');
        header("Location:".$url);
    }

    public static function filterPost() {
        if (isset($_POST['title']) && $_POST['title'] != '') {
            $title = $_POST['title'];
            $icon = isset($_POST['icon']) ? $_POST['icon'] : '';
            $type = isset($_POST['type']) ? $_POST['type'] : '';
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        } else {
            $res['errCode'] = 0;
            echo WebUtils::outputWebApi($res, '', true);
        }
        $data = array(
            'title' => $title,
            'icon' => $icon,
            'type' => $type,
            'keyword' => $keyword
        );
        return $data;
    }

}