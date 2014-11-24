<?php 
/**
 * module_mobile_ui_fastpost view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */
$component = $module;
?>
<div class="content-list-ui">
        <div style="height:498px;padding-top: 260px;background-color: grey;">
            <?php 
            $count = count($component['componentList']);
            if($count == 6 || $count == 3){
                     foreach($component['componentList'] as $k => $v){   $icon = $v['icon'];?>
                    <div onclick="redirfastUrl(<?php echo $k;?>);" style="width:33%;height:110px;float: left;padding-top: 15px;">
                        <div>
                            <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                        </div>
                        <div style="height: 30px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6);?></div>
                    </div>
                  <?php }}else if($count == 4 ||  $count == 2){ ?>
                    <div  style="width: 230px;margin: auto;">
                     <?php foreach($component['componentList'] as $k => $v){  $icon = $v['icon']; ?>
                        <div onclick="redirfastUrl(<?php echo $k;?>);" style="width:50%;height:110px;float: left;margin: 0 auto;">
                            <div>
                                <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                            </div>
                            <div style="height: 30px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6);?></div>
                        </div>
                     <?php }?>
                   </div>
                   <?php }else if($count == 1){ 
                         foreach($component['componentList'] as $k => $v){   $icon= $v['icon']; ?>
                         <div onclick="redirfastUrl(<?php echo $k;?>);" style="margin: 0 auto;height:110px;">
                            <div>
                                <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                            </div>
                            <div style="height: 30px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6);?></div>
                        </div>
                        <?php }}else{  foreach($component['componentList'] as $k => $v){
                              $icon = $v['icon'];
                              if($k <=2){?>
                             <div onclick="redirfastUrl(<?php echo $k;?>);" style="width:33%;height:110px;float: left;padding-top: 15px;">
                                <div>
                                    <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                                </div>
                                <div style="height: 30px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6);?></div>
                            </div>
                            <?php }else{?>
                               <div onclick="redirfastUrl(<?php echo $k;?>);"  style="width:40%;height:110px;float: left;padding-left:50px;">
                                  <div>
                                      <img style="width:70px;height: 70px;" src="<?php echo $this->getComponentIconUrl($icon);?>">
                                  </div>
                                  <div style="height: 30px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6); ?></div>
                               </div>
                        <?php }}} ?>
        </div>
</div>

<script>
    function redirfastUrl(i){
       var moduleInfo =
    <?php echo WebUtils::jsonEncode($module,'utf-8');?>    
    ;
       var moduleInfo = moduleInfo['componentList'][i];
        $.ajax({
                        type:"POST",
                        url:Appbyme.getAjaxApiUrl('admin/uidiy/componentmobileui'),
                        data:{
                            component: JSON.stringify(moduleInfo),
                        },
                        dataTyle:"html",
                        success:function(msg) {
                            $('.module-mobile-ui-view').html(msg);
                        }
                    });
        }
</script>
