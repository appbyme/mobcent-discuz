<?php  

/**
 * module_mobile_ui view
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

$this->renderPartial('module_mobile_ui/' . $module['type'], array(
    'module' => $module,
));

?>

<script>
$('.uidiy-mobileui-component').click(function () {
    Backbone.ajax({
        url: uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy/componentmobileui',
        type: 'post',
        data: {component: $(this).data('componentData')},
        dataType: 'html',
        success: function (result, status, xhr) {
            $('.module-mobile-ui-view').html(result).removeClass('hidden');
        }
    });
});
</script>