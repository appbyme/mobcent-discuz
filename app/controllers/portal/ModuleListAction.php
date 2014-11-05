<?php

/**
 * 门户资讯分类模块列表
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ModuleListAction extends MobcentAction {

    public function run() {
        $res = WebUtils::initWebApiArray_oldVersion();
        $res['list'] = $this->_getModuleList();
        echo WebUtils::outputWebApi($res, '', false);
    }

    // 获取门户资讯列表
    private function _getModuleList() {
        // 封装:PortalUtils.php,6:截取的数组长度 Date:2014/11/4
        return PortalUtils::getModuleList(6);
    }
}