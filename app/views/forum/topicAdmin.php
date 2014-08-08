<?php header("Content-Type: text/html; charset=utf-8");?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Cache-Control" content="no-transform" />
<link rel="alternate" type="application/vnd.wap.xhtml+xml" media="handheld" href="target"/>
<meta name="viewport" content="width=device-width,user-scalable=no,minimum-scale=1.0,initial-scale=1.0">
<script type="text/javascript" src="<?php echo $this->rootUrl.'/js/'; ?>jquery-2.0.3.min.js"></script>
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
    var errorMsg = '<?php echo $errorMsg; ?>';
    if (errorMsg != '') {
        alert(errorMsg);
    }
});
</script>
</head>
<body>
<div class="zhiding">
<form method="post" action="<?php echo $formUrl; ?>">
<table cellpadding="0" cellspacing="0" class="fwin" width="100%">
    <tr>
        <td class="m_c">
            <div class="tm_c">
                <h3 class="flb">
                    <em>选择了 1 篇帖子</em>
                </h3>
                <div class="c">
                    <?php  if ($action == 'delete') { ?> 
                    <div class="tplw">
                        <ul class="llst">
                            <li><p>您确认要 <strong>删除</strong> 选择的帖子么?</p></li>
                        </ul>
                    </div>
                    <?php } else { ?>
                    <ul class="tpcl">
                        <?php  if ($action == 'top') {?>
                        <li class="copt">
                            <table cellspacing="0" cellpadding="5" width="100%">
                                <tr>
                                    <td class="hasd">
                                        <label class="labeltxt">置顶</label>
                                        <div class="dopt">
                                        <select class="ps" name="sticklevel">
                                        <?php if($_G['forum']['status'] != 3) { ?>
                                        <option value="0">无</option>
                                        <option value="1" <?php echo $stickcheck['1'];?>><?php echo WebUtils::u($_G['setting']['threadsticky']['2']);?></option>
                                        <?php if($_G['group']['allowstickthread'] >= 2) { ?>
                                        <option value="2" <?php echo $stickcheck['2'];?>><?php echo WebUtils::u($_G['setting']['threadsticky']['1']);?></option>
                                        <?php if($_G['group']['allowstickthread'] == 3) { ?>
                                        <option value="3" <?php echo $stickcheck['3'];?>><?php echo WebUtils::u($_G['setting']['threadsticky']['0']);?></option>
                                        <?php } } } else { ?>
                                        <option value="0">否&nbsp;</option>
                                        <option value="1" <?php echo $stickcheck['1'];?>>是&nbsp;</option>
                                        <?php } ?>
                                        </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="dopt" style="display:none">
                                    <td>
                                        <p class="hasd">
                                            <label for="expirationstick" class="labeltxt">有效期</label>
                                            <input type="text" id="expirationstick" name="expirationstick" class="px" value="" tabindex="1" />
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </li>
                        <?php } else if ($action == 'marrow') {?>
                        <li class="copt">
                            <table cellspacing="0" cellpadding="5" width="100%">
                                <tr>
                                    <td class="hasd">
                                        <label class="labeltxt">精华</label>
                                        <div class="dopt">
                                        <select name="digestlevel">
                                        <option value="0">解除</option>
                                        <option value="1" <?php echo $digestcheck['1'];?>>精华 1</option>
                                        <?php if($_G['group']['allowdigestthread'] >= 2) { ?>
                                        <option value="2" <?php echo $digestcheck['2'];?>>精华 2</option>
                                        <?php if($_G['group']['allowdigestthread'] == 3) { ?>
                                        <option value="3" <?php echo $digestcheck['3'];?>>精华 3</option>
                                        <?php } } ?>
                                        </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="dopt" style="display:none">
                                    <td>
                                        <p class="hasd">
                                            <label for="expirationdigest" class="labeltxt">有效期</label>
                                            <input type="text" id="expirationdigest" name="expirationdigest" class="px" value="" tabindex="1" />
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                    <div class="tpclg">
                        <h4 class="cl">
                            <span>操作原因:</span>
                            <select id="reasonSelect">
                                <option value="-1">请选择</option>
                                <option>广告/SPAM</option>
                                <option>恶意灌水</option>
                                <option>违规内容</option>
                                <option>文不对题</option>
                                <option>重复发帖</option>
                                <option>--------</option>
                                <option>我很赞同</option>
                                <option>精品文章</option>
                                <option>原创内容</option>
                                <option value="0">自定义</option>
                            </select>
                        </h4>
                        <p><textarea id="reason" name="reason" class="pt" rows="3"></textarea></p>
                    </div>
                </div>
                <p class="o pns">
                    <?php if ($action == 'delete') {?>
                    <label for="crimerecord">
                        <input type="checkbox" name="crimerecord" id="crimerecord" class="pc" fwin="mods">违规登记
                    </label>
                    <?php } ?>
                    <label for="sendreasonpm">
                        <input type="checkbox" name="sendreasonpm" id="sendreasonpm" class="pc" style="margin-right:5px;">通知作者
                    </label>
                </p>
                <p style="text-align:center">
                    <button type="submit" name="modsubmit" id="modsubmit" class="pn pnc" value="确定" ><span>确定</span>
                    </button>
                </p>
            </div>
        </td>
    </tr>
</table>
</form>
</div>
</body>
</html>