<!DOCTYPE html>
<html>
<head>
	<?php global $_G; ?>
	<meta charset="<?php echo $_G['charset'] ?>">
	<title>rate_list</title>
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
			margin-top: 5px;
			text-align: right;
		}
		td{
			padding: 5px;
			margin-left: 5px;
			width: 75px;
			border-bottom: 1px dashed gray;
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
		<h4 style="color:#369;padding-left:5px;"><?php echo WebUtils::t('查看全部评分'); ?></h4>
		<hr/>
		<table>
			<tr>
				<td><?php echo WebUtils::t('积分'); ?></td>
				<td><?php echo WebUtils::t('用户名'); ?></td>
				<td><?php echo WebUtils::t('时间'); ?></td>
				<td class="reason"><?php echo WebUtils::t('理由'); ?></td>
			</tr>
			<?php foreach($loglist as $k => $v): ?>
			<?php global $_G; ?>
			<tr>
				<td><?php echo $_G['setting']['extcredits'][$v['extcredits']]['title'].$v['score']; ?></td>
				<td><?php echo $v['username'] ?></td>
				<td><?php echo $v['dateline'] ?></td>
				<td><?php echo $v['reason']; ?></td>
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