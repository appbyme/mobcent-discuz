<?php 
/**
 * component_mobile_ui_newslist view
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */
    $moduleId = $component['extParams']['newsModuleId'];
    $url = $this->rootUrl."/index.php?r=portal/newslist&hacker_uid=1&sdkVersion=1.2.2&page=1&pageSize=20&moduleId=". $moduleId;
    $info = WebUtils::httpRequest($url, 30);
    $info = WebUtils::jsondecode($info);
?>

<?php if(!empty($info['piclist'])) { ?>
<div class="tab-content">
    <div class="tab-pane fade in active" id="home" >
        <!-- 幻灯片，id使用的为bootstrap默认的id -->
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- 幻灯片上面的圆点 -->
            <ol class="carousel-indicators" style="top:95px;">
                <?php foreach($info['piclist'] as $k => $v){ ?>
                <li data-target="#carousel-example-generic" data-slide-to="<?php echo $k; ?>" class="<?php echo ($k==0)? 'active' : '' ;?>"></li>
               <?php } ?>
            </ol>
            <!-- 幻灯片图片 -->
            <div class="carousel-inner">
                <!-- 幻灯片具体图片 -->
                <?php foreach($info['piclist'] as $k => $v){ ?>
                <div class="img-size item <?php echo ($k==0)? 'active' : '' ;?>">
                    <img  src="<?php echo str_replace('xgsize','mobcentSmallPreview',$v['pic_path']);?>" alt="" style="width:336px;height:116px;">
                    <div class="carousel-caption">
                        <p><?php echo $v['title']; ?></p> 
                    </div>
                </div>
                <?php } ?>
            </div>
            <!-- 幻灯片end -->

            <!-- 左右两个切换按钮 -->
            <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
        </div>
        <!-- 内容 -->
        <div class="content-list-add" style="height: 380px;">
            <div class="list-group">
                <?php foreach($info['list'] as $k => $v){ ?>
                <div style="height:92px;padding-left: 10px;border-bottom: 1px solid #C9C9C9;margin-top:8px;">
                    <h5 class="list-group-item-heading text-left"><?php echo WebUtils::subString($v['title'],0,22,'utf-8');?></h5>
                    <div class="middle-div">
                        <?php if($v['pic_path']){?>
                        <img src="<?php echo str_replace('xgsize','mobcentSmallPreview',$v['pic_path']);?>"  class="pull-right img-rounded img-set">
                        <p class="list-group-item-text pull-left text-left" style="width:260px;">
                        <?php echo $v['summary'];?>
                        </p>
                        <?php }else{?>
                        <p class="list-group-item-text pull-left text-left" >
                        <?php echo $v['summary'];?>
                        </p>
                        <?php } ?>
                    </div>
                    <div class="icon-size">
                        <span class="pull-left span-left"><?php echo date("Y-m-d H:i:s", str_replace('000','',$v['last_reply_date'])); ?></span>
                        <div class="pull-right">
                            <img class="footer-img span-left" src="<?php echo $this->rootUrl;?>/images/admin/tmp/view.png">
                            <span class="span-left"><?php echo $v['hits']; ?></span>
                            <img class="footer-img span-left" src="<?php echo $this->rootUrl;?>/images/admin/tmp/reply.png">
                            <span class="span-left"><?php echo $v['replies'];?></span>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
        <?php } else { ?>
        <div class="content-list-add" style="height: 495px;">
            <div class="list-group">
            <?php foreach($info['list'] as $k => $v){ ?>
            <div style="height:95px;padding-left: 10px;border-bottom: 1px solid #C9C9C9;margin-top:8px;">
                <h5 class="list-group-item-heading text-left"><?php echo WebUtils::subString($v['title'],0,22,'utf-8');?></h5>
                <div>
                <?php if($v['pic_path']){?>
                <img src="<?php echo str_replace('xgsize','mobcentSmallPreview',$v['pic_path']);?>"  class="pull-right img-rounded img-set">
                <p class="list-group-item-text pull-left text-left" style="width:260px;">
                <?php echo $v['summary'];?>
                </p>
                <?php }else{?>
                <p class="list-group-item-text pull-left text-left">
                <?php echo $v['summary'];?>
                </p>
                <?php } ?>
                </div>
                <div class="footer-icon">
                    <span class="pull-left span-left"><?php echo date("Y-m-d H:i:s", str_replace('000','',$v['last_reply_date'])); ?></span>
                    <div class="pull-right">
                        <img class="footer-img" src="<?php echo $this->rootUrl;?>/images/admin/tmp/view.png">
                        <span class="span-left"><?php echo $v['hits']; ?></span>
                        <img class="footer-img span-left"  src="<?php echo $this->rootUrl;?>/images/admin/tmp/reply.png">
                        <span class="span-left"><?php echo $v['replies'];?></span>
                    </div>
                </div>
            </div>
            <?php  } ?>
            </div>
        </div>
        <?php } ?>