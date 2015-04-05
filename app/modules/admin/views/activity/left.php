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
<body>
    <ul class="nav nav-pills nav-stacked">

        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">微营销管理<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li role="presentation"><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/reward/rewardlist" target="main">邀请注册管理</a></li>
                <li class="divider"></li>
                <li role="presentation"><a>抽奖活动管理</a></li>
                <li class="divider"></li>
                <li role="presentation"><a>限时抢购活动管理</a></li>
            </ul>
        </li>

        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">统计管理<span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li role="presentation"><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/reward/rewardcount&show=1" target="main">邀请注册统计</a></li>
                <li class="divider"></li>
                <li role="presentation"><a>抽奖活动统计</a></li>
                <li class="divider"></li>
                <li role="presentation"><a>限时抢购活动统计</a></li>
            </ul>
        </li>

    </ul>

</body>