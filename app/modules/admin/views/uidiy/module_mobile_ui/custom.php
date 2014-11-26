<?php
/**
 * 自定义页面模板 
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy/module-custom.css">
<!-- 自定义页面样式开始 -->
<div class="custom" >
    <!-- 风格区 -->
    <?php foreach ($module['componentList'] as $customStyle): ?>
    <div class="custom-style">
        <!-- 标题是否显示 -->
        <?php if ($customStyle['extParams']['styleHeader']['isShow'] == 1 && $customStyle['extParams']['styleHeader']['position'] == 1): ?>
        <div class="custom-style-title">
            <p class="pull-left"><?php echo WebUtils::subString($customStyle['extParams']['styleHeader']['title'], 0, 15, 'UTF-8'); ?></p>
            <?php if ($customStyle['extParams']['styleHeader']['isShowMore'] == 1): ?>
                <p class="pull-right moreComponent uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($customStyle['extParams']['styleHeader']['moreComponent'], 'utf-8')); ?>">更多</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php foreach ($customStyle['componentList'] as $component): ?>
            <?php if ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_ONE_COL_HIGH): ?>
            <!-- 单栏样式高 -->
            <div class="layout-one-col-high">
                <?php foreach($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <img src="<?php echo $comp['icon'] ?>" style="width:320px;height:320px;" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                <?php elseif($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <img src="<?php echo $comp['icon'] ?>" class="img-rounded textImage uidiy-mobileui-component" style="width:320px;height:300px" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                    <p class="textImage-title text-left"><?php echo WebUtils::subString($comp['desc'], 0, 15, 'UTF-8'); ?></p>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="textOverlapDown">
                        <img src="<?php echo $comp['icon'] ?>" style="width:320px;height:320px;" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div class="textOverlapDown-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 15, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_ONE_COL_LOW): ?>
            <!-- 单栏样式低 -->
            <div class="custom-style-layout-one-col-low">
                <?php foreach ($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <img src="<?php echo $comp['icon'] ?>" style="width:320px;height:160px;" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <img src="<?php echo $comp['icon'] ?>" class="img-rounded textImage uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                    <p class="textImage-title text-left"><?php echo WebUtils::subString($comp['desc'], 0, 15, 'UTF-8'); ?></p>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="textOverlapDown">
                        <img src="<?php echo $comp['icon'] ?>" style="width:320px;height:160px;" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div class="textOverlapDown-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 15, 'UTF-8'); ?></div>
                        </div>
                    </div>                    
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_TWO_COL_TEXT): ?>
            <!-- 双栏文字 -->
            <div class="custom-layouttwocoltext">
                <?php foreach ($component['componentList'] as $comp): ?>
                    <div class="layouttwocoltext-title uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>"><?php echo WebUtils::subString($comp['desc'], 0, 10, 'UTF-8'); ?></div>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_TWO_COL_HIGH): ?>
            <!-- 双栏样式高 -->
            <div class="layouttwocol-high">
                <?php foreach ($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                <div class="layouttwocol-hight-img">
                    <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:155px;">
                </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                <div class="layouttwocol-hight-img">
                    <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:135px;">
                    <div><?php echo WebUtils::subString($comp['desc'], 0, 8, 'UTF-8'); ?></div>
                </div>                    
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                <div class="layouttwocol-hight-img">
                    <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:155px;">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 8, 'UTF-8'); ?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_TWO_COL_MID): ?>
            <!-- 双栏样式中 -->
            <div class="layouttwocol-mid">
                <?php foreach ($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-two-col-mid">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:130px;">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>    <div class="custom-style-layout-two-col-mid">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:110px;">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, 8, 'UTF-8'); ?></div>
                    </div>         
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-two-col-mid">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:130px;">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 8, 'UTF-8'); ?></div>
                        </div>
                    </div>      
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_TWO_COL_LOW): ?>
            <!-- 双栏样式低 -->
            <div class="layout-two-col-low">
                <?php foreach ($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-two-col-low">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:70px;">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-two-col-low">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:50px;">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, 8, 'UTF-8'); ?></div>
                    </div>
                <?php elseif($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-two-col-low">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:155px;height:70px;">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 8, 'UTF-8'); ?></div>
                        </div>
                    </div>   
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_THREE_COL_TEXT): ?>
            <!-- 三栏文字 -->
            <div class="custom-layoutThreeColText">
                <?php foreach ($component['componentList'] as $comp): ?>
                    <div class="layoutthreecoltext-title uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>"><?php echo WebUtils::subString($comp['desc'], 0, 6, 'UTF-8'); ?></div>
                <?php endforeach; ?>
            </div> 
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_THREE_COL_HIGH): ?>
            <!-- 三栏样式高 -->
            <div class="layout-three-col-high">
                <?php foreach ($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-three-col-high">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:130px;">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-three-col-high">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:110px;">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, 5, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>   
                    <div class="custom-style-layout-three-col-high">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:130px;">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 5, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_THREE_COL_MID): ?>
            <!-- 三栏样式中 -->
            <div class="layout-three-col-mid">
                <?php foreach ($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-three-col-mid">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:100px;">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-three-col-mid">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:80px;">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, 5, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-three-col-mid">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:100px;">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 5, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_THREE_COL_LOW): ?>
            <!-- 三栏样式低 -->
            <div class="layout-three-col-low">
                <?php foreach ($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-three-col-low">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:70px;">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-three-col-low">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:50px;">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, 5, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-three-col-low">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:100px;height:70px;">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 5, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_FOUR_COL): ?>
            <!-- 四栏样式 -->
            <div class="layout-four-col">
                <?php foreach ($component['componentList'] as $comp): ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-four-col">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:75px;height:75px;">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-four-col">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:75px;height:55px;">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, 4, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-four-col">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>" style="width:75px;height:75px;">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, 4, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_ONE_COL_ONE_ROW): ?>
            <!-- 1(大)+1样式 -->
            <div class="layout-one-col-one-row">
                <?php foreach ($component['componentList'] as $key => $comp): ?>
                <?php list($className, $count) = ($key == 0) ? array('left', '10') : array('right', 5); ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-one-col-one-row-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-one-row-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-one-col-one-row-<?php echo $className ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-one-row-<?php echo $className; ?>-text-image uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-one-col-one-row-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-one-row-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_ONE_COL_TWO_ROW): ?>
            <!-- 1+2 -->
            <div class="layout-one-col-two-row">
                <?php foreach ($component['componentList'] as $key => $comp): ?>
                <?php list($className, $count) = ($key == 0) ? array('left', '10') : array('right', 5); ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-one-col-two-row-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-two-row-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-one-col-two-row-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-two-row-<?php echo $className; ?>-text-image uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-one-col-two-row-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-two-row-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_ONE_COL_THREE_ROW): ?>
            <!-- 1+3 -->
            <div class="layout-one-col-three-row">
                <?php foreach ($component['componentList'] as $key => $comp): ?>
                <?php list($className, $count) = ($key == 0) ? array('left', '10') : array('right', 4); ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-one-col-three-row-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-three-row-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-one-col-three-row-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-three-row-<?php echo $className; ?>-text-image uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-one-col-three-row-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-col-three-row-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_ONE_ROW_ONE_COL): ?>
            <!-- 1+1(大) -->
            <div class="layout-one-row-one-col">
                <?php foreach ($component['componentList'] as $key => $comp): ?>
                <?php list($className, $count) = ($key == 0) ? array('left', '5') : array('right', '10'); ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-one-row-one-col-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-row-one-col-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-one-row-one-col-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-row-one-col-<?php echo $className; ?>-text-image uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-one-row-one-col-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-one-row-one-col-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                        </div>
                    </div> 
                <?php endif; ?> 
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_TWO_ROW_ONE_COL): ?>
            <!-- 2+1 -->
            <div class="layout-two-row-one-col">
                <?php foreach ($component['componentList'] as $key => $comp): ?>
                <?php list($className, $count) = (in_array($key, array(0,1))) ? array('left', '5') : array('right', '10'); ?>
                    <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                        <div class="custom-style-layout-two-row-one-col-<?php echo $className; ?>">
                            <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-two-row-one-col-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        </div>
                    <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                        <div class="custom-style-layout-two-row-one-col-<?php echo $className; ?>">
                            <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-two-row-one-col-<?php echo $className; ?>-text-image uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                            <div><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                        </div>
                    <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                        <div class="custom-style-layout-two-row-one-col-<?php echo $className; ?>">
                            <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-two-row-one-col-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                            <div class="custom-covering-title">
                                <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>                    
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_THREE_ROW_ONE_COL): ?>
            <!-- 3+1 -->
            <div class="layout-three-row-one-col">
                <?php foreach ($component['componentList'] as $key => $comp): ?>
                <?php list($className, $count) = (in_array($key, array(0,1,2))) ? array('left', '5') : array('right', '10'); ?>
                <?php if ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_IMAGE): ?>
                    <div class="custom-style-layout-three-row-one-col-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-three-row-one-col-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_IMAGE): ?>
                    <div class="custom-style-layout-three-row-one-col-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-three-row-one-col-<?php echo $className; ?>-text-image uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                    </div>
                <?php elseif ($comp['iconStyle'] == AppbymeUIDiyModel::COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN): ?>
                    <div class="custom-style-layout-three-row-one-col-<?php echo $className; ?>">
                        <img src="<?php echo $comp['icon'] ?>" class="img-rounded layout-three-row-one-col-<?php echo $className; ?> uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                        <div class="custom-covering-title">
                            <div class="textoverlapdown-color"><?php echo WebUtils::subString($comp['desc'], 0, $count, 'UTF-8'); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php elseif ($component['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_SLIDER): ?>
            <!-- 幻灯片 -->
            <div class="carousel slide carousel-example-generic_one" data-ride="carousel" data-interval="3000" style="width:337px;height:150px;">
                    <!-- 圆点 -->
                    <ol class="carousel-indicators">
                        <?php foreach ($component['componentList'] as $key => $comp): ?>
                            <li data-target=".carousel-example-generic_one" data-slide-to="<?php echo $key; ?>" class="<?php echo ($key == 0) ? 'active' : '' ?>"></li>
                        <?php endforeach; ?>
                    </ol>
                    <!-- 图片区域，item是一个图片 -->
                    <div class="carousel-inner">
                        <?php foreach ($component['componentList'] as $key => $comp): ?>
                        <div class="item <?php echo ($key == 0) ? 'active' : '' ?>">
                            <img src="<?php echo $comp['icon']; ?>" alt="" style="width:337px;height:150px;" class="uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($comp, 'utf-8')); ?>">
                            <div class="carousel-caption">
                                <p><?php echo WebUtils::subString($comp['desc'], 0, 10, 'UTF-8'); ?></p> 
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <a class="left carousel-control" href=".carousel-example-generic_one" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href=".carousel-example-generic_one" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($customStyle['extParams']['styleHeader']['isShow'] == 1 && $customStyle['extParams']['styleHeader']['position'] == 0): ?>
        <div class="custom-style-title">
            <p class="pull-left"><?php echo WebUtils::subString($customStyle['extParams']['styleHeader']['title'], 0, 15, 'UTF-8'); ?></p>
            <?php if ($customStyle['extParams']['styleHeader']['isShowMore'] == 1): ?>
                <p class="pull-right moreComponent uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($customStyle['extParams']['styleHeader']['moreComponent'], 'utf-8')); ?>">更多</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div><!-- 风格区结束 -->
    <?php endforeach; ?>

</div><!-- 自定义页面整体结束 -->
<script type="text/javascript">
    $(function() {
        $('.carousel-example-generic_one').carousel();
    })
</script>