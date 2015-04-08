<!DOCTYPE html>
<html>
<head>
    <title>添加邀请注册</title>
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
            <div class="panel-heading"><b>添加邀请注册</b></div>
            <div class="panel-body">
                <form class="form-horizontal" action="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/reward/rewardadd" method="post">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">主办方：</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="sponsor" id="" required autofocus autocomplete="off">
                            <span id="helpBlock" class="help-block"><small>将在客户端显示</small></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">活动开始时间：</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="startTime" id="startTime" required autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">活动结束时间：</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="stopTime" id="stopTime" placeholder="" required autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">首次安装奖励金额：</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="firstReward" placeholder="" required autocomplete="off">
                            <span id="helpBlock" class="help-block"><small>首次安装客户端初始的奖励金额</small></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">邀请注册奖励金额：</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="inviteReward" required autocomplete="off">
                            <span id="helpBlock" class="help-block">每邀请一个用户注册可用得到的奖励</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">兑换最低额度:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="exchangeMin" required autocomplete="off">
                            <span id="helpBlock" class="help-block">兑换手机话费或者是论坛虚拟货币的起点</span>
                        </div>
                    </div>

<!--                     <div class="form-group ">
                        <label class="col-sm-2 control-label">兑换类型: </label>
                        <div class="col-sm-10">
                            <select name="exchangeType" class="form-control">
                                <option value="virtual">论坛虚拟货币</option>
                                <option value="mobile">手机话费</option>
                            </select>
                        </div>
                    </div> -->

                    <div class="form-group ">
                        <label for="" class="col-sm-2 control-label">兑换虚拟货币名称:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="virtualName" autocomplete="off">
                            <span id="helpBlock" class="help-block">
                            参与活动得到的奖励能兑换那种论坛虚拟币，比如金币、威望或者是其他自定义的货币名称
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">兑换虚拟货币比例:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="exchangeRatio" autocomplete="off">
                            <span id="helpBlock" class="help-block">如果选择兑换类型为金额，填入100。意思就是 1个金额 = 100个金币</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">活动规则:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="activityRule" autocomplete="off">
                            <span id="helpBlock" class="help-block">链接到论坛的一个帖子，格式为http://xxx</span>
                        </div>
                    </div>

                     <div class="form-group">
                        <label for="" class="col-sm-2 control-label">分享下载地址:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="shareAppUrl" autocomplete="off" value="<?php echo isset($inviteInfo['share_appurl'])?$inviteInfo['share_appurl']:'' ?>">
                            <span id="helpBlock" class="help-block">输入APP下载地址，格式为http://xxx</span>
                        </div>
                    </div>                   

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">防作弊限制:</label>
                        <div class="col-sm-10">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="limitUser" checked value="1"> 用户
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="limitDevice" checked value="1"> 设备
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="limitTime" checked  disabled value="1"> 时间
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">时间（天数）:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="limitDays" autocomplete="off" disabled>
                            <span id="helpBlock" class="help-block">默认是开始时间-结束时间，最大不能超过此值</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">次数（兑换次数）:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="limitNum" autocomplete="off" value="1" disabled>
                            <span id="helpBlock" class="help-block">此活动默认设置为一次</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">添 加</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="panel-footer"></div>
        </div>
    </div>
</body>
<script>
    $( "#startTime" ).datepicker();
    $( "#stopTime" ).datepicker();
</script>