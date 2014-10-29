<!DOCTYPE html>
<html>
<head>
    <title>安米后台管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-theme-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy.css">
    <style type="text/css">
        .mobleShow {
            width: 380px;
            background:url("<?php echo $this->rootUrl; ?>/images/admin/moble.png") no-repeat right top;
            text-align: center;
        }
    </style>
</head>
<body>

<?php global $_G; ?>
    <!-- Static navbar -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">APPbyme</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav nav-list">
            <li class=""><a href="<?php echo $this->dzRootUrl; ?>" target="_blank">网站首页</a></li>
            <li><a href="http://addon.discuz.com/?@appbyme_app.plugin.doc/install" target="_blank">说明文档</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/plugin.php?id=appbyme_app:download" target="_blank">应用下载</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/requirements/index.php" target="_blank">配置需求</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/admin.php" target="_blank">Discuz!管理中心</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_G['username']; ?> <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="<?php echo Yii::app()->createAbsoluteUrl('admin/index/logout'); ?>">退出</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    
    <div id="uidiy-main-view">

    <div class="container">
        <div class="row">

            <div class="col-md-4">
                <div class="mobleShow">

                    <div class="moble-content"> 

                        <div id="navitem-edit-dlg-view" class="play-border-add">
                        </div>
                        <div id="navitem-remove-dlg-view" class="play-border-add">
                        </div>

                        <!-- 发现下方加号弹出框 -->
                        <div id="module-topbar-dlg-view" class="play-add-plug">
                        </div>

                        <!-- 左图右文添加/编辑弹出框 -->
                        <div class="pic-text-pop">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title pull-left">添加内容</h3>
                                    <button type="button" class="close pic-text-pop-close pull-right">&times;</button>
                                </div>
                                <form class="form-horizontal navitem-edit-form">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">导航名字：</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control">
                                                <p class="help-block">输入1-4个字母、数字或汉字</p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">内容简介：</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" rows="3" style="resize:none;margin-bottom:8px;"></textarea>
                                            </div>
                                        </div>

                                         <div class="form-group">
                                            <label for="" class="col-sm-4 control-label">编辑图标：</label>
                                            <div class="col-sm-8">
                                                <input type="file" id="" >
                                                <p class="help-block">上传1:1比例的JPG或PNG格式</p>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-4 col-sm-8 text-left">
                                                <img src="" style="width:50px;height:50px;" class="img-rounded">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="panel-footer text-right">
                                    <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
                                    <button type="button" class="btn btn-default btn-sm pic-text-pop-close">取 消</button>
                                </div>
                            </div>
                        </div>

                        <img class="hidden" src="<?php echo $this->rootUrl; ?>/images/admin/moble-bg.png">

                        <div id="module-edit-mobile-view">
                        </div>
                        
                        <!-- 手机底部导航 -->
                        <div class="moble-bottom-nav">
                            <?php foreach ($navInfo['navItemList'] as $navItem) { ?>
                            <div class="pull-left nav-column>" style='background:url("<?php echo $this->rootUrl.'/images/admin/'.$navItem['icon'].'.png' ?>") no-repeat 50% 20%'>
                                <small class="navitem-title"><?php echo $navItem['title']; ?></small>
                            </div>
                           <?php } ?>
                        </div>
                        </div>

                    </div><!-- end moble-content -->
                </div>
            </div>
        </div>
    </div>

    </div>

    </script>
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->rootUrl; ?>/js/jquery-ui-1.11.2.min.js"></script>
    <!-- 页面弹出样式用到的js -->
    <script type="text/javascript">
        $(function() {
        })
    </script>
</body>
</html>