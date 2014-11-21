<?php 
/**
 * module_mobile_ui_subnav view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */
$title = $module['title'];
$subnavInfo = $module['componentList'];
$j = $module['select'] ? intval($module['select']) : 0;
?>
<ul class="nav nav-justified" style="background-color: #545354;height:37px;">
	<?php foreach($subnavInfo as $k =>$v){?>
	<li onclick="getProtalInfo(<?php echo $k; ?>);" >
		<a  data-toggle="tab" ><?php echo $v['title']; ?></a>
	</li>
	<?php } ?>
</ul>
 <?php $this->renderPartial('component_mobile_ui', array(
            'component' => $subnavInfo[$j],
            ));
 ?>

 <script>
function getProtalInfo(i){
       var moduleInfo =  <?php echo WebUtils::jsonEncode($module,'utf-8');?>;
       moduleInfo['select'] = i;
        $.ajax({
                        type:"POST",
                        url: "<?php echo $this->rootUrl; ?>/index.php?r=admin/uidiy/modulemobileui",
                        data:{
                            module: JSON.stringify(moduleInfo),
                        },
                        dataTyle:"html",
                        success:function(msg) {
                            $('.module-mobile-ui-view').html(msg);
                        }
                    });
        }
 </script>