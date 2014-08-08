<?php
$appInfo = $options;
$appName = $appInfo['appName'];
$appIcon = $appInfo['appIcon'];
$appImage = $appInfo['appImage'];
$androidDownloadUrl = $appInfo['appDownloadUrl']['android'];
$appleDownloadUrl = $appInfo['appDownloadUrl']['appleMobile'];
$assetsBaseUrlPath = $this->rootUrl.'/assets/download';
// var_dump($appInfo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $appName;?></title>
<style type="text/css">
*{margin:0;padding:0;}
*, *:before, *:after {
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
}
.clear{zoom:1;}
.clear:after{ content:"."; display:block; height:0; visibility:hidden;clear: both; }
body{ font-family:"微软雅黑";}
.bg{width:100%;height:100%; background:#000; filter:alpha(opacity=50);-moz-opacity:0.5; 
opacity:0.5;position:absolute;left:0;top:0;display: none;}
.alertdiv{padding:0 10px;position:absolute;left:0;top:0;z-index:10;width:100%;display:none;}
.alertbg{width:100%;padding:50px 0 30px; background:#fff; color:#000; text-align:center; font-size:2em;-webkit-border-bottom-left-radius:10px;-webkit-border-bottom-right-radius:10px;-moz-border-bottom-left-radius:10px;-moz-border-bottom-right-radius:10px;-o-border-bottom-left-radius:10px;-o-border-bottom-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;position:relative;}
.right{background: url("<?php echo $assetsBaseUrlPath ?>/images/adorn-weixin.png") no-repeat; width:308px;height:158px; overflow:hidden;position:absolute;right:50px;top:40px;}
.alertboxs h2{ font-weight:normal;padding-bottom:10px;}
.alertboxs p{ line-height:45px;}
.blue{ color:#0e2ce2;}
.red{ color:#d20909;}

body {
    margin: 0px;
    padding: 0px;
    height: 100%;
}

#all {
    height: 100%;
    position: absolute;
    width: 100%;
}

#z1img {
    height: 80%;
    vertical-align: middle;
}

#z1 {
    height: 10%;
    background: #f6f6f6;
    line-height: 100%;
    padding-left: 3%;
    font-size: xx-large;
}

#z1 table {
    height: 100%;
}

#z2 {
    background: #FFFFFF;
    height: 78%;
    line-height: 100%;
    padding: 0% 3%;
    text-align: center;
}

#z2 img {
    height: 90%;
}

#z3table {
    height: 12%;
    width: 100%;
    background: #f6f6f6;
    position: relative;
    padding: 0% 3%;
}

#z3table a {
    display: block;
}

#z3table img {
    width: 90%;
}

.alertbox {
    width: 70%;
    height: 200px;
    overflow: hidden;
    border: 2px solid #999;
    position: fixed;
    left: 40%;
    top: 50%;
    margin-left: -25%;
    margin-top: -50px;
    display: none;
    background: #fff;
    font-size: 2em;
}

.alertcon {
    height: 140px;
    overflow: hidden;
}

.alertcon p {
    line-height: 44px;
}

.alertbtn {
    width: 100%;
    height: 24px;
    line-height: 24px;
}

.alertbtn input {
    width: 40%;
    height: 60px;
    margin: 0 auto;
    display: block;
    text-align: center;
}
</style>
</head>
<script type="text/javascript">
function showWechatHint() { 
    document.getElementById('weixinbox').style.display = 'block';
    document.getElementById('bg').style.display = 'block';
}
function hideWechatHint() { 
    document.getElementById('weixinbox').style.display = 'none';
    document.getElementById('bg').style.display = 'none';
}
</script>
<body>
    <div id="all">
        <div id="z1">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                    <img src="<?php echo $appIcon; ?>" id="z1img" />
                    <?php echo $appName; ?>
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" id="z2">
            <tr>
                <td>
                    <img src="<?php echo $appImage;?>" />
                </td>
            </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0"id="z3table">
            <tr>
                <td width="50%" height="100%" align="left">
                    <a <?php echo !MobileDetectUtils::isMicroMessenger() ? 'href="'.$androidDownloadUrl.'"' : 'onclick="showWechatHint();"'; ?>>
                        <img src="<?php echo $assetsBaseUrlPath; ?>/images/android_an.gif" alt="">
                    </a>
                </td>
                <td width="50%" height="100%" align="right">
                    <a <?php echo !MobileDetectUtils::isMicroMessenger() ? 'href="'.$appleDownloadUrl.'"' : 'onclick="showWechatHint();"'; ?>>
                       <img src="<?php echo $assetsBaseUrlPath; ?>/images/iphone_an.gif" alt="">
                    </a>
                </td>
            </tr>
        </table>
        <div id="z3"></div>
    </div>
    <div class="bg" id="bg" onclick="hideWechatHint();"></div>
        <div class="alertdiv" id="weixinbox">
            <div class="alertbg">
                <div class="alertboxs clear">
                    <h2><?php echo WebUtils::t('点击不能下载？'); ?></h2>
                    <p><?php echo WebUtils::t('请点击右上角'); ?>
                        <span class="blue"><?php echo WebUtils::t('【三个点图标】'); ?></span>
                    </p>
                    <p><?php echo WebUtils::t('选择'); ?>
                        <span class="red"><?php echo WebUtils::t('“在浏览器（安卓版）或者Safari（ios版）中打开”'); ?>
                        </span>
                    </p>        
            </div>
            <div class="right"></div>
        </div>
    </div>
</body>
</html>