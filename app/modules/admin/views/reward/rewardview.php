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
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/jquery-ui.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/activity/tuiguang.css">
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-ui-1.11.2.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-switch-3.2.1.min.js"></script>
</head>
<body>
    <div class="reward-edit">
        <div class="panel panel-default">
            <div class="panel-heading"><b>邀请注册活动相关配置</b></div>
            <div class="panel-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">活动ID：</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $inviteInfo['activity_id']; ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">主办方：</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['sponsor'])?$inviteInfo['sponsor']:'' ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">活动开始时间：</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo date('Y/m/d', $inviteInfo['start_time']) ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">活动结束时间：</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo date('Y/m/d', $inviteInfo['stop_time']) ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">首次安装奖励金额：</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['first_reward'])?$inviteInfo['first_reward']:'' ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">邀请注册奖励金额：</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['invite_reward'])?$inviteInfo['invite_reward']:'' ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">兑换最低额度:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['exchange_min'])?$inviteInfo['exchange_min']:'' ?></p>
                        </div>
                    </div>
<!--                     <div class="form-group ">
                        <label class="col-sm-2 control-label">兑换类型: </label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $inviteInfo['exchange_type'] == 'mobile' ? '手机话费':'论坛虚拟货币'; ?></p>
                        </div>
                    </div> -->


                    <div class="form-group ">
                        <label for="" class="col-sm-2 control-label">兑换虚拟货币名称:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['virtual_name'])?$inviteInfo['virtual_name']:'' ?></p>
                        </div>
                    </div>

                    <div class="form-group scale">
                        <label for="" class="col-sm-2 control-label">兑换虚拟货币比例:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['exchange_ratio'])?$inviteInfo['exchange_ratio']:'' ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">活动规则:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['activity_rule'])?$inviteInfo['activity_rule']:'' ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">分享下载地址:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['share_appurl'])?$inviteInfo['share_appurl']:'' ?></p>
                        </div>
                    </div>                   

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">防作弊限制:</label>
                        <div class="col-sm-10">
                            <label class="checkbox-inline">
                                <input type="checkbox" id="inlineCheckbox1" <?php echo $inviteInfo['limit_user'] ? checked : '' ?> value="1" disabled > 用户
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" id="inlineCheckbox2" <?php echo $inviteInfo['limit_device'] ? checked : '' ?> value="1" disabled > 设备
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" id="inlineCheckbox3" <?php echo $inviteInfo['limit_time'] ? checked : '' ?> value="1" disabled > 时间
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">时间（天数）:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['limit_days'])?$inviteInfo['limit_days']:'' ?></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">次数（兑换次数）:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo isset($inviteInfo['limit_num'])?$inviteInfo['limit_num']:'' ?></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel-footer"></div>
        </div>
    </div>
</body>