<?php 
/**
 * component_mobile_ui_recommenduserlist view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */
    /*$url   = $this->rootUrl."/index.php?r=user/userlist&type=recommend&pageSize=10&page=1&accessToken=8d5478c77477933169ab8cfde10b5&accessSecret=a57002aab240f3ff831d868b623ff";
    $info = WebUtils::httpRequest($url, 30);
    $info = WebUtils::jsondecode($info);*/
?>
<div class="content-list-ui">
    <img src="<?php echo $this->rootUrl; ?>/images/admin/tmp/recommenduserlist.png" style="width:336px;height: 493px;">
</div>
<!--<div class="content-list-ui content-list-add">
    <?php foreach($info['list'] as $k =>
    $v){?>
    <div class="div-line">
        <img class="pull-left img-set" src="<?php echo $v['icon'];?>
        ">
        <div class="pull-left span-set">
            <span class="span-right">
                <?php echo $v['name'];?></span>
            <span>
                <?php echo ($v['gender'] == 1) ? '男' : (($v['gender'] == 2) ? '女' : '保密');?></span>
        </div>
        <div class="pull-right">
            <img class="img-set-right" src="<?php echo $this->
            rootUrl;?>/images/admin/tmp/msg.png">
            <img class="img-set-right" src="<?php echo $this->
            rootUrl.($v['is_friend'] ? '/images/admin/tmp/follow.png' : '/images/admin/tmp/unfollow.png');?>">
        </div>
    </div>
    <?php }?>
</div>-->