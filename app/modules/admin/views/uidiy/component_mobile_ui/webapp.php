<?php

/**
 * component_mobile_ui_app view
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

?>
<style type="text/css">
    .wap-iframe {
        width: 335px;
        height: 498px;
        overflow-y:hidden;
    }
</style>
<iframe src="<?php echo $component['extParams']['redirect']; ?>" class="wap-iframe"></iframe>
