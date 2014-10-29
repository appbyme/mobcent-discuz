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

                        <img class="moble-top-show" src="<?php echo $this->rootUrl; ?>/images/admin/moble-nav.png">

                        <div class="module-content" style="width:320px;height:503px;padding-top:10px;">

                        </div>

                        <!-- 手机底部导航 -->
                        <div class="moble-bottom-nav">

                            <?php foreach ($navInfo['navItemList'] as $navItem) { ?>
                            <div class="nav-item" id="<?php echo $navItem['moduleId']; ?>">
                                <div class="pull-left nav-column" style='
                                background:url("<?php echo $this->rootUrl.'/images/admin/mobile_icon2/'.$navItem['icon'].'_h.png' ?>") no-repeat 50% 20%;background-size:60px 50px'>
                                    <small class="navitem-title"><?php echo $navItem['title']; ?></small>
                                    <div class="nav-edit hidden">
                                        <a><span class="navitem-edit-btn"><small>编辑</small></span></a>
                                        <a><span class="navitem-remove-btn"><small>删除</small></span></a>
                                    </div>
                                </div>
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
                    var modules = <?php echo WebUtils::jsonEncode($modules, 'utf-8'); ?>;
            $('.nav-item').on({
                click:function() {
                    var moduleId = $(this).attr('id');
                    var moduleInfo = modules[moduleId][0];
                    // console.log(moduleInfo);
                    $.ajax({
                        type:"POST",
                        url: "<?php echo $this->rootUrl; ?>/index.php?r=admin/uidiy/modulemobileui",
                        data:{
                            module: JSON.stringify(moduleInfo),
                        },
                        dataTyle:"html",
                        success:function(msg) {
                            $('.module-content').html(msg);
                        }
                    });
                },
            })
        })
    </script>
</body>
</html>