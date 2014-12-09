<?php

/**
 * component_mobile_ui_userlist view
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

$imgUrl = $this->rootUrl . '/images/admin/tmp/' . ($component['extParams']['orderby'] == AppbymeUIDiyModel::USERLIST_ORDERBY_DISTANCE ? 'surroundinguser' : 'recommenduserlist') . '.png';
?>

<div class="content-list-ui">
    <img src="<?php echo $imgUrl; ?>" style="width:336px;height: 493px;">
</div>