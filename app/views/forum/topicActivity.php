<?php header("Content-Type: text/html; charset=utf-8");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Cache-Control" content="no-transform" />
<link rel="alternate" type="application/vnd.wap.xhtml+xml" media="handheld" href="target"/>
<meta name="viewport" content="width=device-width,user-scalable=no,minimum-scale=1.0,initial-scale=1.0">
<script type="text/javascript" src ="<?php echo $this->dzRootUrl; ?>/static/js/common.js"></script>
<script type="text/javascript">var STYLEID = '1', STATICURL = 'static/', IMGDIR = 'static/image/common', VERHASH = 'D1M', charset = 'utf-8', discuz_uid = '1', cookiepre = 'HVza_2132_', cookiedomain = '', cookiepath = '/', showusercard = '1', attackevasive = '0', disallowfloat = 'newthread', defaultstyle = '', SITEURL = '<?php echo $this->dzRootUrl; ?>/', JSPATH = '<?php echo $this->dzRootUrl; ?>/static/js/';</script>
<script type="text/javascript">
function _showdistrict(container, elems, totallevel, changelevel, containertype) {
    var getdid = function(elem) {
        var op = elem.options[elem.selectedIndex];
        return op['did'] || op.getAttribute('did') || '0';
    };
    var pid = changelevel >= 1 && elems[0] && $(elems[0]) ? getdid($(elems[0])) : 0;
    var cid = changelevel >= 2 && elems[1] && $(elems[1]) ? getdid($(elems[1])) : 0;
    var did = changelevel >= 3 && elems[2] && $(elems[2]) ? getdid($(elems[2])) : 0;
    var coid = changelevel >= 4 && elems[3] && $(elems[3]) ? getdid($(elems[3])) : 0;
    var url = SITEURL+"home.php?mod=misc&ac=ajax&op=district&container="+container+"&containertype="+containertype
        +"&province="+elems[0]+"&city="+elems[1]+"&district="+elems[2]+"&community="+elems[3]
        +"&pid="+pid + "&cid="+cid+"&did="+did+"&coid="+coid+'&level='+totallevel+'&handlekey='+container+'&inajax=1'+(!changelevel ? '&showdefault=1' : '');
    ajaxget(url, container, '');
    }
    function ajaxinnerhtml(showid, s) {
    s=s.replace(new RegExp("&nbsp;&nbsp;","gm"),"")
    if(showid.tagName != 'TBODY') {
        showid.innerHTML = s;
    } else {
        while(showid.firstChild) {
            showid.firstChild.parentNode.removeChild(showid.firstChild);
        }
        var div1 = document.createElement('DIV');
        div1.id = showid.id+'_div';
        div1.innerHTML = '<table><tbody id="'+showid.id+'_tbody">'+s+'</tbody></table>';
        $('append_parent').appendChild(div1);
        var trs = div1.getElementsByTagName('TR');
        var l = trs.length;
        for(var i=0; i<l; i++) {
            showid.appendChild(trs[0]);
        }
        var inputs = div1.getElementsByTagName('INPUT');
        var l = inputs.length;
        for(var i=0; i<l; i++) {
            showid.appendChild(inputs[0]);
        }
        div1.parentNode.removeChild(div1);
    }
}
function hiddenDiv(){
$("messageDiv").style.display="none";
}
</script>
<!-- <script type="text/javascript" src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script> -->
<style type="text/css">
 html {
    -webkit-text-size-adjust: none;
}
* {
    margin: 0;
    padding: 0;
}
input, select, textarea {
    margin: 0;
    padding: 0;
    font-size: 14px;
    outline:none;
    border: 1px solid #ccc;
    padding: 5px;
    font-family: "微软雅黑","宋体",Arial,Verdana,sans-serif;
}
body {
    font-size: 14px;font-family: "Hiragino Sans GB","Microsoft YaHei","微软雅黑","宋体",Arial,Verdana,sans-serif;color: #505050;
}
table {
    border-collapse: collapse;
    width: 100%;
}
input, select {
    height: 20px;
    line-height: 20px;
    -webkit-border-radius:3px;
    -moz-border-radius:3px;
    -ms-border-radius:3px;
    border-radius:3px;
}
select{width:100%;height:32px;line-height:32px;}
td, th {
    text-align: left;
    height: 30px;
    line-height: 30px;
    vertical-align: middle;
    padding: 2px 0;
}
th {
    width: 85px;
    font-weight:normal;
     text-align:right;
     padding-right:3px;
     padding-left:5px;
}
a{ text-decoration:none;}
.y {
    color: red;
    font-style: normal;
    padding: 0 3px;
    vertical-align: middle;
}
#box{width:320px;margin:0 auto;}
.inp_aa{ width:100%;}
.inp_aa select{ display:block;}
.inp_aa textarea{ line-height:20px;}
#birthyear,#birthmonth{margin-bottom:6px;}
#birthday{margin-bottom:2px;}
.inp_aa input,.inp_aa textarea{width:95%;}
.inp_button{width:80px;height:32px; line-height:32px; background:#6da136; color:#fff;display:inline-block;padding:0;-webkit-border-radius: 3px;
-moz-border-radius: 3px;border-radius: 3px;margin:5px 0;border:none;}
/* 平板电脑和小屏电脑之间的分辨率 */
@media (min-width: 768px){ 

}
/* 横向放置的手机和竖向放置的平板之间的分辨率 */
@media (max-width: 767px) {

}
/* 横向放置的手机及分辨率更小的设备 */
@media (max-width: 480px) { 
#box{width:100%;margin:0 auto;}
.inp_aa{width:95%;}
.inp_aa input, .inp_aa textarea,.inp_aa select{width:99%;}
.inp_aa input, .inp_aa textarea{width:95%;}
 }
 .alert{
    width:180px;height:80px;padding:10px; overflow:hidden;border:1px solid #ccc;position:fixed;left:50%;top:50%;margin-left:-100px;margin-top:-50px;background:#fff;
    display: <?php echo $errorMsg != '' ? $errorMsg : 'none'; ?>;
}
 .textdiv{height:60px; overflow:hidden;}
 .btndiv{height:20px; text-align:center; line-height:20px;}
 .btndiv a{width:40px;height:18px; line-height:18px; overflow:hidden; display:inline-block; text-align:center; background:#ccc; color:#505050;}
</style>
</head>
<body>
<div id="box" >
<br /><h2 class="flb"><em><?php echo WebUtils::u($data['title']); ?></em></span></h2><br />
<form method="post" action="<?php echo $formUrl; ?>">
<p class="xi1"><?php echo WebUtils::u($data['description']); ?></p> <br />
<table summary="我要参加" cellpadding="0" cellspacing="0" class="actl">
  <tbody>
    <?php
    $html = '';
    foreach ($data['options'] as $option) {
        $html .= sprintf('
            <tr>
                <th id="th_%s"><strong class="rq y">%s</strong>%s&nbsp;&nbsp;&nbsp;</th>
                <td id="td_%s">
        ',
            $option['name'], isset($option['attributes']['required']) && $option['attributes']['required'] == 1 ? '*' : '',
            WebUtils::u($option['label']), $option['name']
        );
        switch ($option['type']) {
            case 'radio':
                if (!empty($option['elements'])) {
                    foreach ($option['elements'] as $i => $element) {
                        $checked = $option['value'] == $element['value'] ? 'checked' : '';
                        $html .= sprintf('
                        <p class="mbn">
                            <label for="%s_%s">
                                <input class="pr" type="radio" value="%s" id="%s_%s" name="%s" %s>%s
                            </label>
                        ',
                            $element['name'], $i, WebUtils::u($element['value']), $element['name'], $i, $element['name'], $checked, WebUtils::u($element['label'])
                        );
                        $subElements = '';
                        if (!empty($element['elements'])) {
                            foreach ($element['elements'] as $subElement) {
                                $subElements .= sprintf('
                                    <input type="%s" name="%s" class="px pxs vm" size="3" onfocus="$(\'%s_%s\').checked = true;" value="%s"> %s
                                ', 
                                    $subElement['type'], $subElement['name'], $element['name'], $i, WebUtils::u($subElement['value']), WebUtils::u($subElement['label'])
                                );
                            }
                        }
                        $html .= sprintf('
                            %s
                        </p>
                        ',
                            $subElements
                        );
                    }
                }
                break;
            case 'text':
                $html .= sprintf('
                    <div class="inp_aa">
                        <input type="text" name="%s" id="%s" class="px" value="%s" tabindex="1">
                    </div>
                ',
                    $option['name'], $option['name'], WebUtils::u($option['value'])
                );
                break;
            case 'select':
                $optionsHtml = '';
                if ($option['name'] == 'residecity' || $option['name'] == 'birthcity') {
                  $key = rtrim($option['name'], 'city');
                  if (!empty($option['value'])) {
                    $html .= sprintf(WebUtils::t(
                        '%s (<a onclick="showdistrict(\'%sdistrictbox\', [\'%sprovince\', \'%scity\', \'%sdist\', \'%scommunity\'], 4, \'\', \'%s\'); return false;" href="javascript:;"> 修改 </a>)
                        <p id="%sdistrictbox" class="inp_aa"></p>
                        '), WebUtils::u($option['value']), $key, $key, $key, $key, $key, $key, $key);
                  } else {
                    $html .= sprintf('<p id="%sdistrictbox" class="inp_aa"></p>
                        <script type="text/javascript">
                        showdistrict(\'%sdistrictbox\', [\'%sprovince\', \'%scity\', \'%sdist\', \'%scommunity\'], 4, \'\', \'%s\');
                        </script>',
                        $key, $key, $key, $key, $key, $key, $key
                    );
                  }
                } else {
                  foreach ($option['elements'] as $element) {
                      $selected = $option['value'] == $element['value'] ? 'selected' : '';
                      $optionsHtml .= sprintf('<option value="%s" %s>%s</option>', WebUtils::u($element['value']), $selected, WebUtils::u($element['label']));
                  }
                  $html .= sprintf('
                      <div class="inp_aa">
                          <select name="%s" id="%s" class="ps" tabindex="1">
                          %s
                      </div>
                  ',
                      $option['name'], $option['name'], $optionsHtml
                  );
                }
                break;
            case 'textarea':
                $html .= sprintf('
                    <div class="inp_aa">
                        <textarea name="%s" id="%s" class="pt" rows="3" cols="40" tabindex="1">%s</textarea>
                    </div>
                ',
                    $option['name'], $option['name'], WebUtils::u($option['value'])
                );
                break;
            default:
                break;
        }
        $html .= sprintf('
                </td>
            </tr>
        ', 
        $option['name']);
    }
    echo $html;
    ?>
    <tr>
        <td colspan="2" style="text-align:center">
          <input type="submit" value="<?php echo '提交'; ?>" class="inp_button" onclick="" />
        </td>
    </tr>
  </tbody>
</table>
</div>
</form>
<div class="alert" id="messageDiv">
<div class="textdiv">
<p><?php echo $errorMsg; ?></p>
<p></p>
</div>
<div class="btndiv"><a href="javascript:hiddenDiv()"><?php echo '确定'?></a></div>
</div>
</body>
</html>
