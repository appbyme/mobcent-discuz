<?php

/**
 * module_mobile_ui_full view
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

$component = $module['componentList'][0];
$this->renderPartial('component_mobile_ui', array(
    'component' => $component,
));