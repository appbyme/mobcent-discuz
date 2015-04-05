<?php 

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ActivityController extends AdminController{

    public function actionIndex() {
        $this->renderPartial('index');
    }

    public function actionTop() {
        $this->renderPartial('top');
    }

    public function actionLeft() {
        $this->renderPartial('left');
    }

    public function actionMain() {
        $this->renderPartial('main');
    }
}
?>