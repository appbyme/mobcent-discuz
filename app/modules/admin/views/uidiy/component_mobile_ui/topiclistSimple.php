<?php 
/**
 * component_mobile_ui_topiclistsimple view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */
    $fid = $component['extParams']['forumId'];
    $url = $this->rootUrl."/index.php?r=forum/topiclist&boardId=".$fid."&pageSize=10&page=1";
    $info = WebUtils::httpRequest($url, 30);
    $info = WebUtils::jsondecode($info);
?>

<div class="content-list-ui content-list-add">
    <div class="list-group">
        <?php foreach($info['list'] as $k =>
        $v){ ?>
        <div onclick="redirPostListUrl();" style="height:95px;padding-left: 10px;border-bottom: 1px solid #C9C9C9;margin-top:8px;">
            <h5 class="list-group-item-heading text-left">
                <?php echo WebUtils::subString($v['title'],0,22);?></h5>
            <div>
                <?php if($v['pic_path']){?>
                <img src="<?php echo str_replace('xgsize','mobcentSmallPreview',$v['pic_path']);?>
                "  class="pull-right img-rounded img-set">
                <p class="list-group-item-text pull-left text-left" style="width:260px;">
                    <?php echo $v['subject'];?></p>
                <?php }else{?>
                <p class="list-group-item-text pull-left text-left">
                    <?php echo $v['subject'];?></p>
                <?php } ?></div>
            <div class="footer-icon">
                <span class="pull-left span-left"><?php echo date("Y-m-d H:i:s", str_replace('000','',$v['last_reply_date'])); ?></span>
                <div class="pull-right">
                    <img class="footer-img" src="<?php echo $this->
                    rootUrl;?>/images/admin/tmp/view.png">
                    <span class="span-left">
                        <?php echo $v['hits']; ?></span>
                    <img class="footer-img span-left"  src="<?php echo $this->
                    rootUrl;?>/images/admin/tmp/reply.png">
                    <span class="span-left">
                        <?php echo $v['replies'];?></span>
                </div>
            </div>
        </div>
        <?php  } ?></div>
</div>
<script>
    function redirPostListUrl(){
         var moduleInfo =
    <?php  $component['title'] = '帖子详情';  $component['type'] = 'postlist'; echo WebUtils::jsonEncode($component,'utf-8');?>    
    ;
               $.ajax({
                        type:"POST",
                        url: Appbyme.getAjaxApiUrl('admin/uidiy/componentmobileui'),
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