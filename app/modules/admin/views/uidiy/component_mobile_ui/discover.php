<?php

/**
 * component_mobile_ui_discover view
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

$sliderComponentList = $component['componentList'][0]['componentList'];
$defaultComponentList = $component['componentList'][1]['componentList'];
$customComponentList = $component['componentList'][2]['componentList'];
$countSliderComponentList = count($sliderComponentList);

?>
<div class="found-module">
    <div class="slide-img ">
        <div class="discover-slider-component-container">
        <?php if ($countSliderComponentList > 0) { ?>
            <div class="carousel slide carousel-example-generic_one" data-ride="carousel" data-interval="3000" style="width:337px;height:150px;">
            <!-- 圆点 -->
                <ol class="carousel-indicators">
                <?php for ($i = 0; $i < $countSliderComponentList; $i++) { ?>
                    <li data-target=".carousel-example-generic_one" data-slide-to="<?php echo $i ?>" class="<?php echo $i == 0 ? 'active' : '' ?>"></li>
                <?php } ?>
                </ol>
                <!-- 图片区域，item是一个图片 -->
                <div class="carousel-inner">
                    <?php for ($i = 0; $i < $countSliderComponentList; $i++) { ?>
                    <div class="item <?php echo $i == 0 ? 'active' : '' ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">
                        <img src="<?php echo $sliderComponentList[$i]['icon']; ?>" alt="" style="width:337px;height:150px;">
                        <div class="carousel-caption">
                            <p><?php echo $sliderComponentList[$i]['title']; ?></p> 
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php if ($countSliderComponentList > 1) { ?>
                <a class="left carousel-control" href=".carousel-example-generic_one" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a class="right carousel-control" href=".carousel-example-generic_one" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
                <?php } ?>
            </div>
        <?php } ?>
        </div>
    </div>
    <div class="found-content">
        <div class="fixed-content">
            <div class="list-group text-left discover-default-component-container">
            <?php foreach ($defaultComponentList as $component) { ?>
                <?php if (!$component['extParams']['isHidden']) { ?>
                <div class="list-group-item uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">
                    <img class="img-rounded pull-left" src="<?php echo $this->getComponentIconUrl($component['icon']); ?>">
                    <div class="pull-left discover-title"><?php echo $component['title']; ?></div>
                </div>
                <?php } ?>
            <?php } ?>
            </div>
        </div>
        <div class="user-content">
            <div class="list-group text-left discover-custom-component-container">
            <?php foreach ($customComponentList as $component) { ?>
                <div class="list-group-item uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">
                    <img class="img-rounded pull-left" src="<?php echo $component['icon']; ?>">
                    <div class="pull-left discover-title"><?php echo $component['title']; ?></div>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>
</div>