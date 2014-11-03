    <div class="moble-top-title">
        <img class="pull-left select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <span><?php echo $module['title'] ?></span>
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
    </div>


    <div class="msg-list">
        <!-- <img class="mobile-icon" src="<?php echo $this->rootUrl; ?>/images/admin/tmp/msg-list.jpg"> -->
    </div>

    <div>
        <ul class="nav nav-justified" style="width:320px;height:30xp;">
            <li class="">
                <a href="#home" data-toggle="tab">Home</a>
            </li>
            <li>
                <a href="#ios" data-toggle="tab">iOS</a>
            </li>
        </ul>

        <!-- tab切换框 -->
        <div class="tab-content">
            <div class="tab-pane fade in active" id="home">
                <!-- 幻灯片，id使用的为bootstrap默认的id -->
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" style="width:320px;height:150px;">
                    <!-- 幻灯片上面的圆点 -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    </ol>

                    <!-- 幻灯片图片 -->
                    <div class="carousel-inner">
                        <!-- 幻灯片具体图片 -->
                        <div class="item active">
                            <img src="<?php echo $this->rootUrl; ?>/images/admin/tmp1.jpg" alt="" style="width:320px;height:150px;">
                            <div class="carousel-caption">
                                <p>predecessor</p> 
                            </div>
                        </div>
                        <div class="item">
                            <img src="<?php echo $this->rootUrl; ?>/images/admin/tmp2.jpg" style="width:320px;height:150px;">
                            <div class="carousel-caption">
                                <p>Similar to the updates Samsung made to the Galaxy S4</p>
                            </div>
                        </div>
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
                <div class="content-list">
                    <div class="list-group">
                      <a href="javascript:void()" class="list-group-item">
                        <h5 class="list-group-item-heading text-left">List group item heading</h5>
                            <img src="" style="width:50px;height:50px;" class="pull-right img-rounded">
                        <p class="list-group-item-text pull-left text-left">
                            这个是摘要的内容这个是摘要的内容这个是摘要的内容这个是摘要的内容这个是摘要的内容这个是摘要的内容这个是摘要的内容这个是摘要的内容这个是摘要的内容
                        </p>
                      </a>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="ios">
                <p>iOS 是一个由苹果公司开发和发布的手机操作系统。最初是于 2007 年首次发布 iPhone、iPod Touch 和 Apple 
            TV。iOS 派生自 OS X，它们共享 Darwin 基础。OS X 操作系统是用在苹果电脑上，iOS 是苹果的移动版本。</p>
            </div>
        </div>
    </div>

