<!DOCTYPE html>
<html>
<head>
    <title>邀请注册统计</title>
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
    <style type="text/css">
        body{
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="panel panel-default">
    <div class="panel-heading"><b>邀请注册统计</b></div>
    <div class="panel-body">
        <form class="form-inline" action="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/reward/rewardcount" method="post">

            <label class="radio-inline">
                <input type="radio" name="type" id="inlineRadio1" checked="checked" value="all"> 全部
            </label>
            <label class="radio-inline">
                <input type="radio" name="type" id="inlineRadio2" value="mobile"> 兑换话费
            </label>
            <label class="radio-inline">
                <input type="radio" name="type" id="inlineRadio3" value="forum"> 兑换论坛货币
            </label>
            &nbsp;&nbsp;
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">用户名</div>
                    <input type="text" class="form-control" id="exampleInputAmount" placeholder="使用用户名查询" name="username">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">查 询</button>
        </form>
    <br>

    <div class="panel panel-default">
    <!-- Default panel contents -->
        <div class="panel-body">
            <p>以下是查询的结果</p>
            <p>目前兑换操作只能在线下手动进行</p>
        </div>
    <!-- Table -->
    <table class="table table-hover">
        <tr>
            <td>序号</td>
            <td>用户名</td>
            <td>邀请用户数</td>
            <td>累计奖励</td>
            <td>可以兑换金额</td>
            <td>手机号码</td>
            <td>申请状态</td>
            <td>申请类型</td>
            <td>兑换</td>
            <td>标记</td>
        </tr>
        <?php $num=1; ?>
        <?php foreach($searchList as $list): ?>
        <tr class="<?php echo $list['exchange_status'] ? success : warning; ?> row-<?php echo $list['uid']; ?>">
            <td><?php echo $num++; ?></td>
            <td><?php echo $list['username'] ?></td>
            <td><?php echo $list['invite_count'] ?></td>
            <td class="reward-sum-<?php echo $list['uid']; ?>"><?php echo $list['reward_sum'] ?></td>
            <td class="available-<?php echo $list['uid']; ?>"><?php echo $list['available_reward'] ?></td>
            <td><?php echo $list['mobile'] ?></td>
            <td class="exchange-status-<?php echo $list['uid']; ?>"><?php echo $list['exchange_status'] == 1 ? 已申请 : 未申请; ?></td>
            <td class="exchange-type-<?php echo $list['uid']; ?>">
                <?php
                    $type = '无';
                    if (in_array($list['exchange_type'], array('mobile', 'forum'))) {
                        $type = $list['exchange_type'] = 'mobile' ? 手机话费 : 虚拟货币;
                    }
                    echo $type;
                ?>
            </td>
            <td>
                <input type="text" style="width:50px" class="exchageNum-<?php echo $list['uid']; ?>">
                <button class="btn btn-primary btn-xs exchange exch-<?php echo $list['uid']; ?>" <?php echo $list['exchange_status'] == 0 ? disabled : ''; ?> data-id=<?php echo $list['uid']; ?>>兑换</button>
            </td>
            <td><button class="btn btn-primary btn-xs flag-btn flag-<?php echo $list['uid']; ?>" data-id=<?php echo $list['uid']; ?>><?php echo $list['flag'] == 1 ? 已标记 : 未标记; ?></button></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <nav>
        <ul class="pager">
            <li>
                <a href=<?php echo $prev; ?>>上一页</a>
            </li>
            <li><a href=<?php echo $next; ?>>下一页</a></li>
        </ul>
    </nav>

    </div>

    </div>
    <div class="panel-footer">
        一共<strong><?php echo $count; ?></strong>条数据 | 
        当前是第<strong><?php echo $page; ?></strong>页 | 
        共<strong><?php echo $maxPage; ?></strong>页

    </div>
</div>
</body>
<script type="text/javascript">
    $(function() {
        var rootUrl = '<?php echo $this->rootUrl; ?>';
        $('.exchange').on({
            click:function() {
                var id = $(this).attr('data-id');
                var exchangeNum = $('.exchageNum-'+id).val();
                var available = $('.available-'+id).html();

                // if (exchangeNum > available) {
                //     alert('ok');
                //     // alert('请输入正确的兑换数值');
                //     return;
                // }

                $.ajax({
                    type:"GET",
                    url:rootUrl+'/index.php?r=admin/reward/rewardexchange&uid='+id+'&exchangeNum='+exchangeNum,
                    dateType:"json",
                    success:function(msg) {
                        var info = JSON.parse(msg);
                        if (info.errCode == 0) {
                            alert(info.errMsg);
                        } else {
                            $('.available-'+id).html(available-exchangeNum);
                            $('.exchange-status-'+id).html('未申请');
                            $('.exchange-type-'+id).html('无');
                            $('.exch-'+id).attr('disabled', 'disabled');
                        }
                    }
                })

            }
        })
        
        $('.flag-btn').on({
            click:function() {
                var id = $(this).attr('data-id');
                console.log(id);
                $.ajax({
                    type:"GET",
                    url:rootUrl+'/index.php?r=admin/reward/flaguser&uid='+id,
                    dateType:"json",
                    success:function(msg) {                      
                        var info = JSON.parse(msg);
                        if (info.errCode == 0) {
                            alert(info.errMsg);
                        } else {
                            $('.flag-'+id).attr('disabled', 'disabled');
                            $('.flag-'+id).html('已标记');
                        }
                    }
                })

            }
        })
    })
</script>