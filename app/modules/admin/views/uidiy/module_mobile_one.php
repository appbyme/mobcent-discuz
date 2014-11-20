
 <?php if($module['componentList'][0]['type'] == 'discover'){
    ?>
    <div class="moble-top-title">
        <img class="pull-left select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <span><?php echo $module['title'] ?></span>
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
    </div>
    <div class="msg-list">
        <img style="width:320px;height:450px;" class="mobile-icon" src="<?php echo $this->rootUrl; ?>/images/admin/tmp/msg-list.jpg"> 
    </div>
    <?php }else if($module['componentList'][0]['type'] == 'forumlist'){
             $url = $this->rootUrl."/index.php?r=forum/forumlist&fid=0";
             $info = WebUtils::httpRequest($url, 30);
             $info = WebUtils::jsondecode($info);
        ?>
        <div class="header-title">
                <span><?php echo $module['title'] ?></span>
        </div>
        <div class="content-list content-list-add">
              <?php foreach($info['list'] as $k => $v){ ?>
                    <div class="row">
                            <div class="board-name">
                                <span><?php echo $v['board_category_name']; ?></span>
                            </div>
                            <?php foreach($v['board_list'] as $kk => $vv){
                                     if($v['board_category_type'] == 2){?>
                            <div  class="col-xs-6 double-div">
                                <div class="row">
                                    <?php if($vv['board_img']){?>
                                    <div class="col-xs-3">
                                        <div  class="board-img">
                                                <img src="<?php echo $vv['board_img'];?>" class="board-img-set">    
                                        </div>
                                    </div>
                                    <div class="col-xs-6 board-content">
                                        <span><?php echo mb_substr($vv['board_name'],0,12);?></span>
                                        <span><?php echo date("Y-m-d", $vv['last_posts_date'] ? str_replace('000','',$vv['last_posts_date']) : time());?></span>
                                    </div>
                                    <div class="col-xs-3 post-num">
                                        <p><?php echo '('.$vv['td_posts_num'].')';?></p>
                                    </div>
                                <?php }else{?>
                                    <div class="col-xs-8 board-content">
                                        <span><?php echo mb_substr($vv['board_name'],0,12);?></span>
                                        <span><?php echo date("Y-m-d", $vv['last_posts_date'] ? str_replace('000','',$vv['last_posts_date']) : time());?></span>
                                    </div>
                                    <div class="col-xs-4 post-num">
                                        <p><?php echo '('.$vv['td_posts_num'].')';?></p>
                                    </div>
                                <?php }?>
                                </div>
                            </div>
                            <?php }else{?>
                                <div class="col-xs-9 double-div">
                                    <div class="row">
                                        <?php if($vv['board_img']){?>
                                            <div class="col-xs-2">
                                                <div  class="board-img">
                                                    <img src="<?php echo $vv['board_img']; ?>" class="board-img-set">
                                                </div>
                                            </div>
                                            <div class="col-xs-10 one-div">
                                                <p><?php echo $vv['board_name'];?></p>
                                                <p><?php echo '最近更新：'.date("Y-m-d", $vv['last_posts_date'] ? str_replace('000','',$vv['last_posts_date']) : time());?></p>
                                            </div>
                                            <?php }else{?>
                                                <div class="col-xs-12 one-div">
                                                     <p><?php echo $vv['board_name'];?></p>
                                                     <p><?php echo '最近更新：'.date("Y-m-d", $vv['last_posts_date'] ? str_replace('000','',$vv['last_posts_date']) : time());?></p>
                                                </div>
                                            <?php }?>
                                    </div>
                                </div>
                                <div class="col-xs-3 post-num1">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <p><?php echo '('.$vv['td_posts_num'].')';?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php }} ?>
                    </div>
            <?php  } ?>
             </div>
    <?php }else if($module['componentList'][0]['type'] == 'topiclist' || $module['type'] == 'topiclist' ){
         $url = $this->rootUrl."/index.php?r=forum/topiclist&boardId=0&pageSize=5&page=1";
         $info = WebUtils::httpRequest($url, 30);
         $info = WebUtils::jsondecode($info);
        ?>
                <div class="header-title">
                    <span><?php echo $module['title'] ?></span>
                </div>
                <div class="content-list">
                        <div class="list-group">
                         <?php foreach($info['list'] as $k => $v){ ?>
                          <a href="javascript:void()" class="list-group-item">
                            <h5 class="list-group-item-heading text-left"><?php echo mb_substr($v['title'],0,60);?></h5>
                            <div>
                            <?php if($v['pic_path']){?>
                            <img src="<?php echo str_replace('xgsize','mobcentSmallPreview',$v['pic_path']);?>"  class="pull-right img-rounded img-set">
                            <p class="list-group-item-text pull-left text-left">
                            <?php echo $v['subject'];?>
                            </p>
                            <?php }else{?>
                            <p class="list-group-item-text pull-left text-left">
                            <?php echo $v['subject'];?>
                            </p>
                            <?php } ?>
                            </div>
                            <div class="footer-icon">
                                <span class="pull-left span-left">5小时前</span>
                                <div class="pull-right">
                                    <img class="footer-img" src="<?php echo $this->rootUrl;?>/images/admin/tmp/a.png">
                                    <span class="span-left"><?php echo $v['hits']; ?></span>
                                    <img class="footer-img span-left"  src="<?php echo $this->rootUrl;?>/images/admin/tmp/b.png">
                                    <span class="span-left"><?php echo $v['replies'];?></span>
                                </div>
                            </div>
                          </a>
                          <?php if($k >=3){break;} } ?>
                        </div>
                </div>
    <?php }else if($module['componentList'][0]['type'] == 'recommendUserlist'){
         $url = $this->rootUrl."/index.php?r=user/userlist&type=recommend&pageSize=10&page=1&accessToken=8d5478c77477933169ab8cfde10b5&accessSecret=a57002aab240f3ff831d868b623ff";
         $info = WebUtils::httpRequest($url, 30);
         $info = WebUtils::jsondecode($info);
        ?>
        <div class="header-title">
                    <span><?php echo $module['title'] ?></span>
        </div>
         <div class="content-list content-list-add">
            <?php foreach($info['list'] as $k => $v){?>
            <div class="div-line">
                <img class="pull-left img-set" src="<?php echo $v['icon'];?>">
                <div class="pull-left span-set">
                    <span class="span-right"><?php echo $v['name'];?></span>
                    <span><?php echo ($v['gender'] == 1) ? '男' : (($v['gender'] == 2) ? '女' : '保密');?></span>
                </div>
                <div class="pull-right">
                     <img class="img-set-right" src="<?php echo $this->rootUrl;?>/images/admin/tmp/d.png">
                     <img class="img-set-right" src="<?php echo $this->rootUrl.($v['is_friend'] ? '/images/admin/tmp/e.png' : '/images/admin/tmp/c.png');?>">
                </div>
            </div>
            <?php }?>
        </div>
<?php }else if($module['componentList'][0]['type']=='surroudingUserlist'){ ?>
    <div class="header-title">
            <span><?php echo $module['title'] ?></span>
    </div>
    <div class="content-list">
        <img src="<?php echo $this->rootUrl; ?>/images/admin/tmp/f.jpg" style="width:320px;height: 450px;">
    </div>
<?php }else if($module['componentList'][0]['type']=='messagelist' || $module['type'] == 'messagelist'){?>
    <div class="header-title">
            <span><?php echo $module['title'] ?></span>
    </div>
    <div class="content-list">
        <img src="<?php echo $this->rootUrl; ?>/images/admin/tmp/msg-list.jpg" style="width:320px;height: 450px;">
    </div>
<?php }else if($module['componentList'][0]['type']=='aboat'){?>
    <div class="header-title">
            <span><?php echo $module['title'] ?></span>
    </div>
    <div class="content-list">
               <ul class="list-unstyled container-fluid">
                    <li  class="list-group-item" style="height: 60px;"><p class="ull-left text-left" >应用介绍：这是一个神奇的简介神奇的简介这是一个神奇的简介神奇的简介这是一个神奇的简介神奇的简介</p></li>
                    <li class="list-group-item" style="height: 60px;"><p class="ull-left text-left" style="line-height: 60px;">反馈邮箱：nmcol@qq.com</p></li>
                    <li class="list-group-item" style="height: 60px;"><p class="ull-left text-left" style="line-height: 60px;">官方网站：http://www.appbyme.com</p></li>
                    <li class="list-group-item" style="height: 60px;"><p class="ull-left text-left" style="line-height: 60px;">新浪微博：http://weibo.com.nmcol</p></li>
                    <li class="list-group-item" style="height: 60px;"><p class="ull-left text-left" style="line-height: 60px;">腾讯微博：http://t.qq.com/nm_col</p></li>
                    <div style="margin-top:10px;">
                    <img src="<?php echo $this->rootUrl; ?>/images/admin/tmp/weixin.jpg" style="width:70px;height: 70px;"/>
                    <p>扫扫我</p>
                    <p>version 1.0.3 bulid 423e</p>
                    <p>扫描二维码即可下载该应用</p>
                </div>
            </ul>
    </div>
<?php }else if($module['componentList'][0]['type']=='surroudingPostlist'){?>
    <div class="header-title">
            <span><?php echo $module['title'] ?></span>
    </div>
    <div class="content-list">
            <img src="<?php echo $this->rootUrl; ?>/images/admin/tmp/g.jpg" style="width:320px;height: 450px;">
    </div>
<?php }else if($module['componentList'][0]['type'] == 'fastpost'){?>
<p>aaaaaaaaaaaaaaa</p>
<?php }else if($module['componentList'][0]['type'] == 'newlist'){?>
<p>bbbbbbbbbbbbbbbb</p>
<?php }else if($module['type'] == 'news'){?>
    
<?php }?>