<!DOCTYPE html>
<html>
<head>
<?php global $_G; ?>
<meta charset="<?php echo $_G['charset'] ?>">
<title>显示全部评分</title>
<link rel="stylesheet" type="text/css" href="<?php echo $this->rootUrl.'/css/'; ?>bootstrap.min.css">
<script type="text/javascript" src="<?php echo $this->rootUrl.'/js/'; ?>jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="<?php echo $this->rootUrl.'/js/'; ?>bootstrap.min.js"></script>
<script type="text/javascript"></script>
<style type="text/css">
    *{
        margin: 0;
        padding: 0;
        word-wrap: break-word;
    }
    h1, h2, h3, h4, h5, h6 {
        font-size: 1em;
    }
    a {
        color: #333;
        text-decoration: none;
    }
    li {
        list-style: none;
    }
    body, input, button, select, textarea {
        font: 12px/1.5 "Hiragino Sans GB", "Microsoft YaHei", "微软雅黑", "宋体", Arial, Verdana, sans-serif;
        color: #444;
    }
    table {
        border-collapse: collapse;
        display: table;
        border-color: gray;
        empty-cells: show;
        /*border-bottom: 1px dashed dashed;*/
    }
    #round{
         width: 320px;
         margin: 10px auto; 
         /*background: #e3e3e3;*/
    }
    #butt{
        /*margin-top: 5px;*/
        text-align: right;
    }
    td{
        padding: 5px;
        margin-left: 5px;
        width: 75px;
        /*border-bottom: 1px dashed gray;*/
    }
    td.reason{
        width: 120px;
    }
    @media (max-width: 480px) {
    #round {
        width: 90%;
        margin: 0 auto;
        padding-left: 5%;
        padding-right: 5%;
    }
    .tpclg .pt {
        width: 97%;
	}
}
</style>
</head>
<body>
	<div id="round">
		<h5 style="color:#369;padding-left:5px;"><?php echo WebUtils::t('查看全部评分'); ?></h5>
		<table class="table table-hover table-condensed">
			<tr>
				<th><?php echo WebUtils::t('积分'); ?></th>
				<th><?php echo WebUtils::t('用户名'); ?></th>
				<th><?php echo WebUtils::t('时间'); ?></th>
			</tr>
			<?php foreach($loglist as $k => $v): ?>
			<?php global $_G; ?>
			<tr>
				<td><?php echo $_G['setting']['extcredits'][$v['extcredits']]['title'].$v['score']; ?></td>
				<td><?php echo $v['username'] ?></td>
				<td><?php echo date('Y/m/d', $v['dateline']) ?></td>
			</tr>
			<?php endforeach; ?>
		</table>

		<p id="butt"><?php echo WebUtils::t('总计:'); ?>
			<?php foreach($logcount as $id => $count): ?>
				<?php echo $_G['setting']['extcredits'][$id]['title']; ?>
				<?php if($count > 0): ?>
			   +<?php echo $count.$_G['setting']['extcredits'][$id]['unit'] ?>,
				<?php endif; ?>
			<?php endforeach; ?>
		</p>
	</div>
</body>
</html>