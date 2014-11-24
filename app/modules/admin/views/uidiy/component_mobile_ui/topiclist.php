<?php 
/**
 * component_mobile_ui_topiclist view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */
    $fid = $component['extParams']['forumId'];
    $sortby = $component['sortby'] ? $component['sortby'] : 'all';
    $url = $this->rootUrl."/index.php?r=forum/topiclist&boardId=".$fid."&pageSize=10&page=1&sortby=".$sortby;
    $info = WebUtils::httpRequest($url, 30);
    $info = WebUtils::jsondecode($info);
?>
<img class="moble-top-show" src="<?php echo $this->rootUrl; ?>/images/admin/moble-nav.png">
    <div class="moble-top-title">
        <img class="pull-left select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <span><?php echo $component['title'];?></span>
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
      </div>
<ul class="nav nav-justified" style="background-color: #545354">
            <li onclick="redirtopicNewListUrl('all');">
                <a data-toggle="tab">全部</a>
            </li>
            <li onclick="redirtopicNewListUrl('all');">
                <a data-toggle="tab">最新</a>
            </li>
            <li onclick="redirtopicNewListUrl('marrow');">
                <a data-toggle="tab">精华</a>
            </li>
            <li onclick="redirtopicNewListUrl('top');">
                <a data-toggle="tab">置顶</a>
            </li>
</ul>
<div class="content-list-add" style="height:455px;">
    <div class="list-group">
        <?php foreach($info['list'] as $k =>
        $v){ ?>
        <div onclick = "redirPostListUrl();" style="height:95px;padding-left: 10px;border-bottom: 1px solid #C9C9C9;margin-top:8px;">
            <h5 class="list-group-item-heading text-left">
                <?php echo mb_substr($v['title'],0,66);?></h5>
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
                <span class="pull-left span-left">5小时前</span>
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
        <?php } ?></div>
</div>
<script>
    function redirtopicNewListUrl(order){
        var moduleInfo =
    <?php  echo WebUtils::jsonEncode($component,'utf-8');?>    
    ;
        moduleInfo['sortby'] = order;
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