<?php

/**
 * component_mobile_ui view
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

$this->renderPartial('component_mobile_ui/' . $component['type'], array(
    'component' => $component,
));