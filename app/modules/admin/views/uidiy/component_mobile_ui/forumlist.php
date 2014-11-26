<?php
/**
 * component_mobile_ui_forumlist view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */
    $fid = $component['extParams']['forumId'];
    $url = $this->rootUrl."/index.php?r=forum/forumlist&fid=".$fid;
    $info = WebUtils::httpRequest($url, 30);
    $info = WebUtils::jsondecode($info);
 ?>

<div class="content-list-ui content-list-add">
<?php foreach($info['list'] as $k => $v){ ?>
    <div class="row">
        <div class="board-name">
            <span><?php echo $v['board_category_name']; ?></span>
        </div>
        <?php foreach($v['board_list'] as $kk => $vv){
                if($v['board_category_type'] == 2) { ?>
                <div  class="col-xs-6 double-div uidiy-mobileui-component" data-component-data="<?php $component['title']='帖子列表';
                $component['type'] = 'topiclist';$component['extParams']['forumId']=$v['board_id'];echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>" >
                    <div class="row">
                    <?php if($vv['board_img']) { ?>
                        <div class="col-xs-3">
                            <div  class="board-img">
                                <img src="<?php echo $vv['board_img']; ?>" class="board-img-set">    
                            </div>
                        </div>
                        <div class="col-xs-6 board-content">
                            <div><?php echo WebUtils::subString($vv['board_name'],0,4,'utf-8'); ?></div>
                            <div><?php echo date("m-d", $vv['last_posts_date'] ? str_replace('000','',$vv['last_posts_date']) : time()); ?></div>
                        </div>
                        <div class="col-xs-3 post-num">
                            <p><?php echo '('.$vv['td_posts_num'].')'; ?></p>
                        </div>
                    <?php } else { ?> 
                        <div class="col-xs-8 board-content">
                            <div><?php echo WebUtils::subString($vv['board_name'],0,4,'utf-8'); ?></div>
                            <div><?php echo date("m-d", $vv['last_posts_date'] ? str_replace('000','',$vv['last_posts_date']) : time()); ?></div>
                        </div>
                        <div class="col-xs-4 post-num">
                            <p><?php echo '('.$vv['td_posts_num'].')'; ?></p>
                        </div>   
                    <?php } ?>
                    </div>
                </div>
                    <?php } else { ?>
                        <div class="uidiy-mobileui-component"  data-component-data="<?php $component['title']='帖子列表';
                        $component['type'] = 'topiclist';$component['extParams']['forumId']=$v['board_id'];echo rawurlencode(WebUtils::jsonEncode($component, 'utf-8')); ?>">
                        <div class="col-xs-9 double-div">
                        <div class="row">
                            <?php if($vv['board_img']) { ?>
                            <div class="col-xs-2">
                                <div  class="board-img">
                                    <img src="<?php echo $vv['board_img']; ?>" class="board-img-set">
                                </div>
                            </div>
                            <div class="col-xs-10 one-div">
                                <p><?php echo WebUtils::subString($vv['board_name'],0,7,'utf-8');?></p>
                                <p><?php echo '最近更新：'.date("m-d", $vv['last_posts_date'] ? str_replace('000','',$vv['last_posts_date']) : time()); ?></p>
                            </div>
                            <?php } else { ?>
                                <div class="col-xs-12 one-div">
                                    <p><?php echo $vv['board_name']; ?></p>
                                    <p><?php echo '最近更新：'.date("m-d", $vv['last_posts_date'] ? str_replace('000','',$vv['last_posts_date']) : time()); ?></p>
                                </div>
                            <?php } ?>
                        </div>
                        </div>
                <div class="col-xs-3 post-num1">
                    <div class="row">
                        <div class="col-xs-12">
                            <p><?php echo '('.$vv['td_posts_num'].')'; ?></p>
                        </div>
                    </div>
                </div>
                </div>
        <?php }} ?>
    </div>
<?php } ?>
</div>
