<!DOCTYPE html>
<html>
<head>
    <title>微生活管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-theme-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-switch-3.2.1.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-wshdiy.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy/component-mobile.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy/module-custom.css">
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-ui-1.11.2.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-switch-3.2.1.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/admin/wshdiy.js"></script>
    <style type="text/css">
        .wshdiy-mobile {
            width: 350px;
            height: 700px;
            background:url("<?php echo $this->rootUrl; ?>/images/admin/mobile.png") no-repeat right top;
            background-size: 350px 700px;
            text-align: center;
            /*border: 1px solid green;*/
            position: relative;
        }
    </style>
</head>
<?php global $_G; ?>
<body>
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="http://www.appbyme.com" target="_blank" style="background:url(<?php echo $this->rootUrl; ?>/images/admin/login.png);width:140px;height:50px;"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav nav-list">
            <li class=""><a href="<?php echo $this->dzRootUrl; ?>" target="_blank">网站首页</a></li>
<!--             <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">应用管理 <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/uidiy">自定义管理</a></li>
                    <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/wshdiy">微生活管理</a></li>
                </ul>
            </li> -->
            <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/uidiy" target="_blank">自定义管理</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/wshdiy" target="_blank">微生活管理</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/activity" >微营销</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/admin.php" target="_blank">Discuz!管理中心</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/plugin.php?id=appbyme_app:download" target="_blank">应用下载</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/requirements/index.php" target="_blank">配置需求</a></li>
            <li><a href="http://bbs.appbyme.com/forum-57-2.html" target="_blank">帮助文档</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="." class="dropdown-toggle" data-toggle="dropdown"><?php echo WebUtils::u($_G['username']); ?> <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="<?php echo Yii::app()->createAbsoluteUrl('admin/index/logout'); ?>">退出</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

</body>