<?php 
/**
 * module_mobile_ui_news view
 *
 * @author hongliang
 * @author 牛存晖 <niucunhui@gmail.com>
 * @copyright 2012-2014 Appbyme
 */
$title = $module['title'];
$newInfo = $module['componentList'];
?>

<div class="pic-text list-group">
    <div class="news-component-item-container">
        <?php foreach ($newInfo as $key => $component) { $icon = $component['icon']; ?>
        <div class="news-component-item list-group-item uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">        
        <div class="pull-left"><img src="<?php echo $icon; ?>" style="width:50px;height:50px" class="img-rounded"></div>
            <div class="pull-left text-left page-main">
                <div class="page-title"><strong><?php echo $component['title']; ?></strong></div>
                <div class="page-content"><?php echo WebUtils::subString($component['desc'],0,26,'utf-8'); ?></div>
            </div>
            <div class="pull-right">
                <span  class="pull-right glyphicon glyphicon-chevron-right"></span>
            </div>       
        </div>
        <?php } ?>
    </div>
</div>
