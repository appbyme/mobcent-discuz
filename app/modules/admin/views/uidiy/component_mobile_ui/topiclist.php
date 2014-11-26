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
<ul class="nav nav-justified" style="background-color: #545354">
    <li class="uidiy-mobileui-component" data-component-data="<?php $component['sortby']='all';
        echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">
        <a data-toggle="tab">全部</a>
    </li>
    <li class="uidiy-mobileui-component" data-component-data="<?php $component['sortby']='new';
        echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">
        <a data-toggle="tab">最新</a>
    </li>
    <li class="uidiy-mobileui-component" data-component-data="<?php $component['sortby']='marrow';
        echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">
        <a data-toggle="tab">精华</a>
    </li>
    <li class="uidiy-mobileui-component" data-component-data="<?php $component['sortby']='top';
        echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">
        <a data-toggle="tab">置顶</a>
    </li>
</ul>
<div class="content-list-add" style="height:455px;">
    <div class="list-group">
        <?php foreach($info['list'] as $k => $v) { ?>
        <div class="uidiy-mobileui-component" data-component-data="<?php $component['title'] = '帖子详情';  $component['type'] = 'postlist';
        echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>" style="height:95px;padding-left: 10px;border-bottom: 1px solid #C9C9C9;margin-top:8px;">
            <h5 class="list-group-item-heading text-left">
                <?php echo WebUtils::subString($v['title'],0,22,'utf-8'); ?>
            </h5>
            <div>
                <?php if($v['pic_path']) { ?>
                <img src="<?php echo str_replace('xgsize','mobcentSmallPreview',$v['pic_path']); ?>
                "  class="pull-right img-rounded img-set">
                <p class="list-group-item-text pull-left text-left" style="width:260px;"><?php echo $v['subject']; ?></p>
                <?php } else { ?>
                <p class="list-group-item-text pull-left text-left"><?php echo $v['subject']; ?></p>
                <?php } ?>
            </div>
            <div class="footer-icon">
                <span class="pull-left span-left"><?php echo date("Y-m-d H:i:s", str_replace('000','',$v['last_reply_date'])); ?></span>
                <div class="pull-right">
                    <img class="footer-img" src="<?php echo $this->
                    rootUrl;?>/images/admin/tmp/view.png">
                    <span class="span-left">
                        <?php echo $v['hits']; ?>
                    </span>
                    <img class="footer-img span-left"  src="<?php echo $this->
                    rootUrl;?>/images/admin/tmp/reply.png">
                    <span class="span-left">
                        <?php echo $v['replies'];?>
                    </span>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
