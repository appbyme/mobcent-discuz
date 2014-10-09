<?php header("Content-Type: text/html; charset=utf-8");?>
<?php global $_G; ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Cache-Control" content="no-transform" />
<link rel="alternate" type="application/vnd.wap.xhtml+xml" media="handheld" href="target"/>
<meta name="viewport" content="width=device-width,user-scalable=no,minimum-scale=1.0,initial-scale=1.0">
<script type="text/javascript" src="<?php echo $this->rootUrl.'/js/'; ?>jquery-2.0.3.min.js"></script>
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
body, input, button, select, textarea, p{
    font: 12px/1.5 "Hiragino Sans GB", "Microsoft YaHei", "微软雅黑", "宋体", Arial, Verdana, sans-serif;
    color: #444;
}
.delect, .zhiding {
    width: 320px;
    margin: 0 auto;
}
table {
    border-collapse: collapse;
    display: table;
    border-color: gray;
    empty-cells: show;
}

#tab1{
    width: 320px;
    /*background: #e3e3e3;*/
    border-radius: 5px;
    text-align: center;
    /*border: 1px solid red;*/
}

#div1{
    margin:20px auto;
    width: 320px;
    /*background: #e3e3e3;*/
    border-radius: 5px;
    height: 100%;
}
select{
    border-radius: 5px;
}

#reason{
    width: 320px;
    margin-top:5px;
    margin-bottom: 5px;
}

#yes{
    width: 40px;
    height: 25px;
    border-radius: 5px;
}
    
#bu1{
    text-align: right;
    line-height: 20px;
}
.list{
    padding: 5px 0 5px 5px;

}

.flb {
    font-size: 14px;
    font-weight: 700;
    color: #369;
    padding-left: 10px;
}
.pc{
    vertical-align: middle;
    margin: 0 5px 1px 0;
    padding: 0;    
}
.pn {
    width: 80px;
    height: 32px;
    line-height: 32px;
    background: #6da136;
    color: #fff;
    display: inline-block;
    padding: 0;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    margin: 5px 0;
    border: none;
}

.labeltxt{
    color:#09C;
}

textarea{
    margin-top: 5px;
    resize:none;
    border-radius: 5px;
}

@media (max-width: 480px) {
#div1, #reason {
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
<script type="text/javascript">
$(function () {
    $('#reasonSelect').on({'change': function () {
        var value = $(this).val();
        if (value != '-1') {
            if (value == '0') {
                $('#reason').get(0).focus();
            } else {
                $('#reason').val(value);
            }
        }
    }});
    var errorMsg = '<?php echo WebUtils::u($errorMsg); ?>';
    if (errorMsg != '') {
        alert(errorMsg);
    }
});
</script>
</head>
<body>
    <div id="div1">
        <form method="post" action="<?php echo $formUrl; ?>">
            <h4 class="flb">评分</h4>
            <table id="tab1">
                <tr>
                    <td colspan="2"></td>
                    <td>评分区间</td>
                    <td>今日剩余</td>
                </tr>
            <?php foreach( $ratelist as $id=>$options): ?>
                <?php
                    $reg = '/[+|-][\w]+/';
                    preg_match_all($reg, $options, $options);
                    $options = $options[0];
                ?>
                <!-- [modify]去掉对于评分今日剩余为0的判断 -->
                <?php //if($options[$id] != ''): ?>
                    <tr>
                        <td><label class="labeltxt"><?php echo WebUtils::u($_G['setting']['extcredits'][$id]['title']);?></label></td>
                        <td class="list">
                            <select name="score<?php echo $id ?>">
                                    <option value="">0</option>
                                <?php foreach($options as $key=>$value): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?php echo $_G['group']['raterange'][$id]['min'];?> ~ <?php echo $_G['group']['raterange'][$id]['max'];?></td>
                        <td><?php echo $maxratetoday[$id] ?></td>
                    </tr>
                <?php //endif; ?>
            <?php endforeach; ?>
            </table>
            <br />
            <span>可选评分理由:</span>
            <select id="reasonSelect">
                <?php foreach($selectreason as $value): ?>
                    <option value="<?php echo WebUtils::u($value); ?>"><?php echo WebUtils::u($value); ?></option>
                <?php endforeach; ?>
                    <option value="0">自定义</option>
            </select>
            <p><textarea id="reason" name="reason" class="pt" rows="3"></textarea></p>
            <p class="o pns">
                <label for="sendreasonpm">
                    <input type="checkbox" name="sendreasonpm" id="sendreasonpm" class="pc" style="margin-right:5px;">通知作者
                </label>
            </p>
            <p style="text-align:center">
                <button type="submit" name="modsubmit" id="modsubmit" class="pn pnc" value="确定" ><span>确定</span>
                </button>
            </p>
        </form>
    </div>
</body>
</html>
