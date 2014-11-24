<?php 
/**
 * module_mobile_ui_news view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */
$title = $module['title'];
$newInfo = $module['componentList'];
?>

<div class="content-list-ui content-list-add">
    <?php foreach($newInfo as $k => $v){ $icon = $v['icon'];?>
<div class="div-img" onclick="redirUrl(<?php echo $k; ?>)">
            <img class="pull-left div-img-size"  src="<?php echo $icon; ?>">
            <div class="pull-left summary">
               <div class="wz-size"><?php echo $v['title']; ?></div>
               <div><?php echo mb_substr($v['desc'],0,72);?></div>
            </div>
            <div class="pull-right">
                 <span  class="pull-right glyphicon glyphicon-chevron-right"></span>
            </div>
</div>
<?php }?>
</div>
<script>
    function redirUrl(i){
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