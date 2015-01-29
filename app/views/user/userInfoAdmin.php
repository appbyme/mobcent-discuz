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
/*table {
    border-collapse: collapse;
    display: table;
    border-color: gray;
    empty-cells: show;
}*/
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
/*.m_c {
    background: #FFF;
}*/
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

.comments { 
width:100%;
overflow:auto; 
word-break:break-all; 
}
textarea
{
width:100%;
height:100%;
}
</style>

</head>
<body>
<div style="margin-top:15px;" class="zhiding">
    <form method="post" action="<?php echo $formUrl; ?>">
        <table cellpadding="0" cellspacing="0" class="fwin" width="100%">
            <tr>
                <?php
                    $base = WebUtils::createUrl_oldVersion('user/userinfoadminview', array('act' => 'base'));
                    $contact = WebUtils::createUrl_oldVersion('user/userinfoadminview', array('act' => 'contact'));
                    $edu = WebUtils::createUrl_oldVersion('user/userinfoadminview', array('act' => 'edu'));
                    $work = WebUtils::createUrl_oldVersion('user/userinfoadminview', array('act' => 'work'));
                    $info = WebUtils::createUrl_oldVersion('user/userinfoadminview', array('act' => 'info'));
                ?>
                <select name="pageselect" onchange="self.location.href=options[selectedIndex].value" >
                    <option value="<?php echo $base;?>" <?php if ($action == "base") {echo "selected='selected'";}?>>基本资料</option>
                    <option value="<?php echo $contact;?>" <?php if ($action == "contact") {echo "selected='selected'";}?>>联系方式</option> 
                    <option value="<?php echo $edu;?>" <?php if ($action == "edu") {echo "selected='selected'";}?>>教育情况</option>
                    <option value="<?php echo $work;?>" <?php if ($action == "work") {echo "selected='selected'";}?>>工作情况</option>
                    <option value="<?php echo $info;?>" <?php if ($action == "info") {echo "selected='selected'";}?>>个人信息</option>
                </select>
            </tr>
            <tr>
                <table border="0" cellspacing="5" cellpadding="0" width="100%">
                    <?php if(is_array($settings)) 
                    foreach($settings as $key => $value) {if($value['available'] && !(in_array($value['fieldid'],array('birthcity','residecity')))) { ?>
                        <?php if ($value['formtype'] != 'textarea') {?>
                        <tr id="tr_<?php echo $key;?>">
                            <th align="left" valign="top" id="th_<?php echo $key;?>">
                            <nobr>
                                <?php if($value['required']) { ?><span class="rq" title="必填">*</span><?php } ?><?php echo WebUtils::u($value['title']);?>
                            </nobr></th>
                            <td class="hasd" id="td_<?php echo $key;?>">
                                <?php echo WebUtils::u($htmls[$key]);?>
                            </td>
                        </tr>
                    <?php } else {?>
                        <tr id="tr_<?php echo $key;?>">
                            <th colspan="2" align="left" id="th_<?php echo $key;?>">
                                <?php if($value['required']) { ?><span class="rq" title="必填">*</span><?php } ?><?php echo WebUtils::u($value['title']);?>
                                <?php echo WebUtils::u($htmls[$key]);?>
                            </th>
                        </tr>
                    <?php } } }?>
                </table>
            </tr>
            <tr>
                <th>
                    <p style="text-align:center">
                        <input type="hidden" name="profilesubmit" value="true">
                        <button type="submit" name="profilesubmitbtn" id="profilesubmitbtn" value="true" class="pn pnc"><strong>保存</strong></button>
                        <span id="submit_result" class="rq"></span>
                    </p>
                </th>
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
