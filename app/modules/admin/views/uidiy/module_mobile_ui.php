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

?>
<!-- @author HanPengyu -->
<img class="moble-top-show" src="<?php echo $this->rootUrl; ?>/images/admin/mobile-nav.png">
<div class="moble-top-title">
    <?php foreach ($module['leftTopbars'] as $leftTopbars): ?>
        <?php if ($leftTopbars['type'] == AppbymeUIDiyModel::COMPONENT_TYPE_WEATHER): ?>
            <img class="pull-left select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($leftTopbars, 'utf-8')); ?>" src="<?php echo $this->rootUrl; ?>/images/admin/topbar/mc_forum_weather_icon2.png">
        <?php elseif($leftTopbars['type'] == AppbymeUIDiyModel::COMPONENT_TYPE_SIGN): ?>
            <div class="pull-left uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($leftTopbars, 'utf-8')); ?>" style="margin:3px 5px 0px 5px;cursor:pointer">签到</div>
        <?php elseif ($leftTopbars['type'] != AppbymeUIDiyModel::COMPONENT_TYPE_EMPTY) : ?>
            <img class="pull-left select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($leftTopbars, 'utf-8')); ?>" src="<?php echo $this->getComponentIconUrl($leftTopbars['icon']); ?>">
        <?php endif; ?>
    <?php endforeach; ?>

    <span><?php echo WebUtils::subString($module['title'], 0, 10); ?></span>

    <?php foreach ($module['rightTopbars'] as $rightTopbars): ?>
        <?php if ($rightTopbars['type'] == AppbymeUIDiyModel::COMPONENT_TYPE_WEATHER): ?>
            <img class="pull-right select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($rightTopbars, 'utf-8')); ?>" src="<?php echo $this->rootUrl; ?>/images/admin/topbar/mc_forum_weather_icon2.png">
        <?php elseif($rightTopbars['type'] == AppbymeUIDiyModel::COMPONENT_TYPE_SIGN): ?>
            <div class="pull-right uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($rightTopbars, 'utf-8')); ?>" style="margin:3px 5px 0px 5px;cursor:pointer;">签到</div>
        <?php elseif ($rightTopbars['type'] != AppbymeUIDiyModel::COMPONENT_TYPE_EMPTY): ?>
            <img class="pull-right select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($rightTopbars, 'utf-8')); ?>" src="<?php echo $this->getComponentIconUrl($rightTopbars['icon']); ?>">
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<!-- @author HanPengyu -->

<?php 
$this->renderPartial('module_mobile_ui/' . $module['type'], array(
    'module' => $module,
));
?>

<script>
$('.uidiy-mobileui-component').click(function () {
    var module = uidiyGlobalObj.moduleInitParams,
        component = JSON.parse(decodeURIComponent($(this).data('componentData')));
    module.title = component.title;
    module.componentList = [
        component,
    ];
    Backbone.ajax({
        url: Appbyme.getAjaxApiUrl('admin/uidiy/modulemobileui'),
        type: 'post',
        data: {
            module: JSON.stringify(module),
        },
        dataType: 'html',
        success: function (result, status, xhr) {
            $('.module-mobile-ui-view').html(result).removeClass('hidden');
        }
    });
});
</script>