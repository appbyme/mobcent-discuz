<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-theme-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-switch-3.2.1.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/activity/tuiguang.css">
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-ui-1.11.2.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-switch-3.2.1.min.js"></script>
</head>
<body>

<div class="panel panel-default">
    <div class="panel-heading"><b>邀请注册管理</b></div>
    <div class="panel-body">
        <div class="activ-list">
            <div class="activ text-center">
                <img src="<?php echo $this->rootUrl.'/images/admin/module-default.png'; ?>" class="img-thumbnail">
                <div><small>邀请注册活动</small></div>
                <div>
                    <a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/activity/rewardview" target="main"><small>查看</small></a>
                    <a href=""><small>编辑</small></a>
                </div>
            </div>

            <div class="activ text-center">
                <a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/activity/rewardcount" target="main"><img src="<?php echo $this->rootUrl.'/images/admin/module-add.png'; ?>" class="img-rounded"></a>
                <div><small>添加邀请注册</small></div>
            </div>

        </div>

    </div>
    <div class="panel-footer">Panel footer</div>
</div>

</body>