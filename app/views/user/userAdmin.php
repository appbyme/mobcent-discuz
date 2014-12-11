<?php header("Content-Type: text/html; charset=utf-8");?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Cache-Control" content="no-transform" />
<link rel="alternate" type="application/vnd.wap.xhtml+xml" media="handheld" href="target"/>
<meta name="viewport" content="width=device-width,user-scalable=no,minimum-scale=1.0,initial-scale=1.0">
<script type="text/javascript" src ="<?php echo $this->dzRootUrl; ?>/static/js/common.js"></script>
<!-- <script type="text/javascript" src="<?php echo $this->rootUrl.'/js/'; ?>jquery-2.0.3.min.js"></script> -->
<style type="text/css">
/* CSS Document */
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
.t_l, .t_c, .t_r, .m_l, .m_r, .b_l, .b_c, .b_r {
    overflow: hidden;
    background: #000;
    opacity: 0.2;
    filter: alpha(opacity=20);
}
.t_l, .t_r, .b_l, .b_r {
    width: 8px;
    height: 8px;
}
.t_l {
    -moz-border-radius: 8px 0 0 0;
    -webkit-border-radius: 8px 0 0 0;
    border-radius: 8px 0 0 0;
}
.t_r {
    -moz-border-radius: 0 8px 0 0;
    -webkit-border-radius: 0 8px 0 0;
    border-radius: 0 8px 0 0;
}
.m_l, .m_r {
    width: 8px;
}
.m_c {
    background: #FFF;
}
.t_c, .b_c {
    height: 8px;
}
.b_l {
    -moz-border-radius: 0 0 0 8px;
    -webkit-border-radius: 0 0 0 8px;
    border-radius: 0 0 0 8px;
}
.b_r {
    -moz-border-radius: 0 0 8px 0;
    -webkit-border-radius: 0 0 8px 0;
    border-radius: 0 0 8px 0;
}
.flb {
    padding: 10px 10px 8px;
    height: 20px;
    line-height: 20px;
    cursor: move;
}
.flb em {
    float: left;
    font-size: 14px;
    font-weight: 700;
    color: #369;
}
em, cite, i {
    font-style: normal;
}
.flb span {
    float: right;
    color: #999;
}
.flb span a, .flb strong {
    float: left;
    text-decoration: none;
    margin-left: 8px;
    font-weight: 400;
    color: #333;
}
/*以上为共用样式*/


/*删除*/
.tplw {
    margin-bottom: 1em;
}
.llst li {
    padding: 4px 0;
    border-bottom: 1px solid #CDCDCD;
}
.pns {
    margin-bottom: 5px;
}
.tpcl {
    margin: 10px 0;
    border-top: 1px solid #DDD;
}
.tpcl li {
    height: 28px;
    line-height: 18px;
    border-top: 1px solid #FFF;
    border-bottom: 1px solid #DDD;
    zoom: 1;
}
.tpcl .copt {
    height: auto;
}
.tpcl table {
    width: 100%;
}
.tpcl td {
    vertical-align: top;
    padding: 5px 0 5px 5px;
}
.pr, .pc {
    vertical-align: middle;
    margin: 0 5px 1px 0;
    padding: 0;
}
.hasd label {
    float: left;
}
.tpcl .labeltxt {
    display: block;
    cursor: pointer;
    width: 100%;
    background: url(img/arrwd.gif) no-repeat 100% 8px;
}
.copt .labeltxt {
    float: left;
    cursor: default;
    width: 45px;
    background: none;
    color: #09C;
    margin-top: 3px;
}
.copt td {
    vertical-align: middle;
}
.dopt {
    visibility: hidden;
    overflow: hidden;
}
.copt .dopt {
    visibility: visible;
dpbtn
}
.px, .pt, .ps, select {
    border: 1px solid #ccc;
}
.ps, select {
    padding: 2px 2px 2px 1px;
}
.px, .pt {
    padding: 2px 4px;
    line-height: 17px;
}
.px {
    height: 17px;
}
.dopt p .px, .hasd .px {
    width: 100px;
    float: left;
}
.dopt a {
    float: left;
    margin-right: 3px;
    width: 21px !important;
    height: 21px;
    line-height: 21px;
    text-align: center;
}
/*以下为共用样式*/
.dpbtn {
    float: left;
    overflow: hidden;
    text-indent: -9999px;
    width: 21px;
    height: 21px;
    border-width: 1px 1px 1px 0;
    border-style: solid;
    border-color: #848484 #E0E0E0 #E0E0E0 #848484;
    background: #FFF url(img/newarow.gif) no-repeat 100% 0;
}
.tpclg {
    padding: 4px 0 4px;
}
.tpclg h4 {
    font-weight: 400;
    width: 222px;
}
.cl {
    zoom: 1;
}
.dpbtn:hover {
    background-position: 100% -23px;
}
.tpclg h4 a.dpbtn {
    float: right;
    border-width: 1px;
}
.tpclg h4 span {
    float: left;
    margin-right: 5px;
    display: inline;
    margin-top: 3px;
}
textarea {
    resize: none;
}
.px, .pt, .ps, select {
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    -ms-border-radius: 3px;
    border-radius: 3px;
}
.pt {
    overflow-y: auto;
}
.tpclg .pt {
    margin: 5px 0;
    width: 97%;
    overflow: hidden;
}
label {
    cursor: pointer;
}
.pr, .pc {
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
a.pn {
    height: 21px;
    line-height: 21px;
    color: #444 !important;
}
.dopt_b, .dopt_i, .dopt_l {
    border: 1px solid #F1F5FA;
    outline: none;
}

/* 平板电脑和小屏电脑之间的分辨率 */
@media (min-width: 768px) {
}

/* 横向放置的手机和竖向放置的平板之间的分辨率 */
@media (max-width: 767px) {
}

/* 横向放置的手机及分辨率更小的设备 */
@media (max-width: 480px) {
.delect, .zhiding {
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
<div class="zhiding">
<form method="post" action="<?php echo $formUrl; ?>">
<table cellpadding="0" cellspacing="0" class="fwin" width="100%">
    <tr>
        <td class="m_c">
            <div class="tm_c">
                <div class="c">
                    <ul class="tpcl">
                        <?php if ($action == 'add') {?>
                        <h3 class="flb">
                            <em id="return_<?php echo $_GET['handlekey'];?>">加为好友</em>
                        </h3>
                            <div class="c">
                            <table>
                                <tr>
                                <th valign="top" width="60" class="avt"><a href="home.php?mod=space&amp;uid=<?php echo $tospace['uid'];?>"><?php echo avatar($tospace[uid],small);?></th>
                                    <td valign="top">添加 <strong><?php echo WebUtils::u($tospace['username']);?></strong> 为好友，附言:<br />
                                    <input type="text" name="note" value="" size="35" class="px"  onkeydown="ctrlEnter(event, 'addsubmit_btn', 1);" />
                                        <p class="mtn xg1">(附言为可选，<?php echo WebUtils::u($tospace['username']);?> 会看到这条附言，最多 10 个字 )</p>
                                        <p class="mtm">
                                        分组: <select name="gid" class="ps"><?php if(is_array($groups)) foreach($groups as $key => $value) { ?><option value="<?php echo $key;?>" <?php if(empty($space['privacy']['groupname']) && $key==1) { ?> selected="selected"<?php } ?>><?php echo WebUtils::u($value);?></option>
                                        <?php } ?>
                                        </select>
                                    </p>
                                </td>
                                </tr>
                            </table>
                            </div>
                        <?php } else if ($action == 'add2') {?>
                            <h3 class="flb">
                                <em id="return_<?php echo $_GET['handlekey'];?>">批准请求</em>
                            </h3>
                            <div class="c">
                                <table cellspacing="0" cellpadding="0">
                                    <tr>
                                    <th valign="top" width="60" class="avt"><a href="home.php?mod=space&amp;uid=<?php echo $tospace['uid'];?>"><?php echo avatar($tospace[uid],small);?></th>
                                    <td valign="top">
                                        <p>批准 <strong><?php echo WebUtils::u($tospace['username']);?></strong> 的好友请求，并分组:</p>
                                        <table><tr><?php $i=0;?><?php if(is_array($groups)) foreach($groups as $key => $value) { ?><td style="padding:8px 8px 0 0;"><label for="group_<?php echo $key;?>"><input type="radio" name="gid" id="group_<?php echo $key;?>" value="<?php echo $key;?>"<?php echo $groupselect[$key];?> /><?php echo WebUtils::u($value);?></label></td>
                                        <?php if($i%2==1) { ?></tr><tr><?php } $i++;?><?php } ?>
                                        </tr></table>
                                    </td>
                                </tr>
                                </table>
                            </div>
                        <?php } else if ($action == 'ignore') {?>
                            <h3 class="flb">
                                <em id="return_<?php echo $_GET['handlekey'];?>">忽略好友</em>
                            </h3>
                            <div class="c">确定忽略好友关系吗？</div>
                        <?php } else if ($action == 'shield') { ?>
                            <h3 class="flb">
                                <em id="return_<?php echo $_GET['handlekey'];?>">屏蔽通知</em>
                            </h3>
                            <div class="c altw">
                                <p>在下次浏览时不再显示此类通知</p>
                                <p class="ptn"><label><input type="radio" name="authorid" id="authorid1" value="<?php echo $_GET['uid'];?>" checked="checked" />仅屏蔽该好友的</label></p>
                                <p class="ptn"><label><input type="radio" name="authorid" id="authorid0" value="0" />屏蔽所有好友的</label></p>
                            </div>
                        <?php } ?>
                    </ul>
                </div>
                <p style="text-align:right">
                    <button type="submit" name="modsubmit" id="modsubmit" class="pn pnc" value="确定" ><span>确定</span>
                    </button>
                </p>
            </div>
        </td>
    </tr>
</table>
</form>
</div>
<script type="text/javascript">
$('reasonSelect').onchange = function () {
    var value = this.value;
    if (value != '-1') {
        if (value == '0') {
            $('reason').focus();
        } else {
            $('reason').value = value;
        }
    }
};
var errorMsg = '<?php echo $errorMsg; ?>';
if (errorMsg != '') {
    alert(errorMsg);
}
</script>
</body>
</html>