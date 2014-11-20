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
    <img class="moble-top-show" src="<?php echo $this->rootUrl; ?>/images/admin/moble-nav.png">
    <div class="moble-top-title">
        <?php foreach ($module['leftTopbars'] as $leftTopbars): ?>
            <?php if ($leftTopbars['type'] == 'weather'): ?>
                <img class="pull-left select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($leftTopbars, 'utf-8')); ?>" src="<?php echo $this->rootUrl; ?>/images/admin/topbar/mc_forum_weather_icon2.png">
            <?php elseif ($leftTopbars['type'] == 'userinfo'): ?>
                <img class="pull-left select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($leftTopbars, 'utf-8')); ?>" src="<?php echo $this->rootUrl; ?>/images/admin/topbar/mc_forum_top_bar_button6_n.png">
            <?php elseif($leftTopbars['type'] == 'sign'): ?>
                <div class="pull-left uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($leftTopbars, 'utf-8')); ?>" style="margin:3px 5px 0px 5px;cursor:pointer">签到</div>
            <?php elseif ($leftTopbars['type'] == 'search'): ?>
                <img class="pull-left select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($leftTopbars, 'utf-8')); ?>" src="<?php echo $this->rootUrl; ?>/images/admin/topbar/mc_forum_top_bar_button10_n.png">
            <?php endif; ?>
        <?php endforeach; ?>

        <span><?php echo WebUtils::subString($module['title'], 0, 10); ?></span>

        <?php foreach ($module['rightTopbars'] as $rightTopbars): ?>
            <?php if ($rightTopbars['type'] == 'weather'): ?>
                <img class="pull-right select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($rightTopbars, 'utf-8')); ?>" src="<?php echo $this->rootUrl; ?>/images/admin/topbar/mc_forum_weather_icon2.png">
            <?php elseif ($rightTopbars['type'] == 'userinfo'): ?>
                <img class="pull-right select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($rightTopbars, 'utf-8')); ?>" src="<?php echo $this->rootUrl; ?>/images/admin/topbar/mc_forum_top_bar_button6_n.png">
            <?php elseif($rightTopbars['type'] == 'sign'): ?>
                <div class="pull-right uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($rightTopbars, 'utf-8')); ?>" style="margin:3px 5px 0px 5px;cursor:pointer;">签到</div>
            <?php elseif ($rightTopbars['type'] == 'search'): ?>
                <img class="pull-right select-topbar-btn uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($rightTopbars, 'utf-8')); ?>" src="<?php echo $this->rootUrl; ?>/images/admin/topbar/mc_forum_top_bar_button10_n.png">
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php 
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