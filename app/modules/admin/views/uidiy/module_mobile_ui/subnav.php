<?php 
/**
 * module_mobile_ui_subnav view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */

$title = $module['title'];
foreach($module['componentList'] as $k =>$v){
    if(!$v['title']){
        unset($module['componentList'][$k]);
    }
}
$subnavInfo = $module['componentList'];
$j = $module['select'] ? intval($module['select']) : 0;
if(!empty($subnavInfo)){
?>
<div style="max-height:496px; overflow:hidden;">
<ul class="nav nav-justified" style="background-color: #545354;height:37px;">
	<?php foreach($subnavInfo as $k =>$v) { ?>
	<li onclick="getProtalInfo(<?php echo $k; ?>);" >
		<a  data-toggle="tab" ><?php echo WebUtils::subString($v['title'],0,4,'utf-8'); ?></a>
	</li>
	<?php } ?>
</ul>
<?php $this->renderPartial('component_mobile_ui', array(
        'component' => $subnavInfo[$j],
        ));
?>
</div>
<?php } ?>
<script>
function getProtalInfo(i){
    var moduleInfo = <?php echo WebUtils::jsonEncode($module,'utf-8'); ?>;
        moduleInfo['select'] = i;
        $.ajax({
            type:"POST",
            url:Appbyme.getAjaxApiUrl('admin/uidiy/modulemobileui'),
            data:{
                module: JSON.stringify(moduleInfo),
            },
            dataType:"html",
            success:function(msg) {
                $('.module-mobile-ui-view').html(msg);
            }
        });
    }
</script>