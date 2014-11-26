<?php 
/**
 * module_mobile_ui_fastpost view
 *
 * @author hongliang
 * @author 牛存晖 <niucunhui@gmail.com>
 * @copyright 2012-2014 Appbyme
 */
$component = $module;
?>
<div class="content-list-ui">
    <div style="height:498px;padding-top: 260px;background-color: grey;">
        <?php $count = count($component['componentList']);
            if($count == 6 || $count == 3){
                foreach($component['componentList'] as $key => $component){   $icon = $component['icon'];?>
                <div class="uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>" style="width:33%;height:110px;float: left;padding-top: 15px;">
                    <div>
                        <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                    </div>
                    <div style="height: 30px;padding-top:5px;"><?php echo WebUtils::subString($component['title'],0,6,'utf-8');?></div>
                </div>
                <?php }}else if($count == 4 ||  $count == 2){ ?>
                    <div  style="width: 230px;margin: auto;">
                    <?php foreach($component['componentList'] as $key => $component){  $icon = $component['icon']; ?>
                        <div class="uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>" style="width:50%;height:110px;float: left;margin: 0 auto;">
                            <div>
                                <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                            </div>
                            <div style="height: 30px;padding-top:5px;"><?php echo WebUtils::subString($component['title'],0,6,'utf-8');?></div>
                        </div>
                    <?php }?>
                   </div>
                <?php }else if($count == 1){ 
                        foreach($component['componentList'] as $key => $component){   $icon= $component['icon']; ?>
                         <div class="uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>" style="margin: 0 auto;height:110px;">
                            <div>
                                <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                            </div>
                            <div style="height: 30px;padding-top:5px;"><?php echo WebUtils::subString($component['title'],0,6,'utf-8');?></div>
                        </div>
                        <?php }}else{  foreach($component['componentList'] as $key => $component){
                              $icon = $component['icon'];
                              if($key <=2){?>
                            <div class="uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>" style="width:33%;height:110px;float: left;padding-top: 15px;">
                                <div>
                                    <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                                </div>
                                <div style="height: 30px;padding-top:5px;"><?php echo WebUtils::subString($component['title'],0,6,'utf-8');?></div>
                            </div>
                            <?php }else{?>
                                <div class="uidiy-mobileui-component" data-component-data="<?php echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>"  style="width:40%;height:110px;float: left;padding-left:50px;">
                                  <div>
                                      <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                                  </div>
                                  <div style="height: 30px;padding-top:5px;"><?php echo WebUtils::subString($component['title'],0,6,'utf-8'); ?></div>
                                </div>
                        <?php }}} ?>
    </div>
</div>
