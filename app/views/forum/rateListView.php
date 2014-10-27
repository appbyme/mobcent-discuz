<!DOCTYPE html>
<html>
<head>
<?php global $_G; ?>
<meta charset="<?php echo $_G['charset'] ?>">
<title><?php echo WebUtils::t('显示全部评分'); ?></title>
<meta name="viewport" content="width=device-width,user-scalable=no,minimum-scale=1.0,initial-scale=1.0">
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
        margin:0 auto;
    }
    th {
    padding: 0 5px;
    text-align: left;
    }
    #round{
         width: 100%;
         margin: 10px auto; 
         /*background: #e3e3e3;*/
         height: 300px;
         /*border: 1px solid red;*/
    }
    #round h5{
        color:#369;
         /*padding-left:30px;*/
        text-align: center;
    }
    #butt{
        /*width: 100%;*/
        margin: 10px auto; 
        /*margin-top: 5px;*/
        text-align: center;
    }
    th{
        text-align:left;
    }
    td{
        padding: 5px;
        margin-left: 5px;
        width: 37%;
        border-bottom: 1px dashed gray;
        text-align:left;
    }
    td.reason{
        width: 120px;
    }
    @media (max-width: 480px) {
    #round {
        width: 90%;
        margin: 5px auto;
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
		<table >
			<tr>
				<th><?php echo WebUtils::t('积分'); ?></th>
				<th><?php echo WebUtils::t('用户名'); ?></th>
				<th><?php echo WebUtils::t('时间'); ?></th>
			</tr>
			<?php foreach($loglist as $k => $v): ?>
			<tr>
				<td><?php echo $_G['setting']['extcredits'][$v['extcredits']]['title'].$v['score']; ?></td>
				<td><?php echo $v['username'] ?></td>
				<td><?php echo date('Y/m/d', $v['dateline']) ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<div id="butt"><?php echo WebUtils::t('总计:'); ?>
			<?php foreach($logcount as $id => $count): ?>
				<?php echo $_G['setting']['extcredits'][$id]['title']; ?>
				<?php echo $count > 0 ? '+'.$count.$_G['setting']['extcredits'][$id]['unit'].',' : $count.$_G['setting']['extcredits'][$id]['unit'].',' ?>
			<?php endforeach; ?> 
		</div>
	</div>
</body>
</html>