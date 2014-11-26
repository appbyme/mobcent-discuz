<?php header("Content-Type: text/html; charset=utf-8");?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>mobcent</title>
    <style type="text/css">
        #topHead{
            background: url("static/images/head_bg.jpg") repeat-x scroll 0 0 transparent;
            height: 70px;
            position: fixed;
            top: 0;
            width: 100%;
            left:0;
        }
            #nav{
                text-align:center;
                margin:0 auto;
                float: left;
                height: 70px;
                padding-left: 20px;
                width: 550px;
            }
            #nav a{
            color: #C8DFF0;
            display: block;
            float: left;
            font-family: Tahoma;
            font-size: 14px;
            font-weight: 600;
            height: 70px;
            line-height: 70px;
            text-align: center;
            width: 80px;
            }
            a:link {
                color: #0174A7;
                text-decoration: none;
            }
    </style>
    <script type="text/javascript">
        window.onload = function() {
            location.href = "./app/web/index.php?r=admin/index";
        }
    </script>
</head>

<body style="margin:0;padding:0;">
    <!--
    <div style="z-index:1000;" id="topHead">
        <div style="margin:0 auto;padding-left:0px;width:1000px;" id="head_nav">
            <div id="nav">
                <a href="../" target="_blank">网站首页</a>
                <a href="http://addon.discuz.com/?@appbyme_app.plugin.doc/install"  target="_blank">说明文档</a>
                <a href="../plugin.php?id=appbyme_app:download" target="_blank">应用下载</a>
                <a href="./requirements/index.php" target="_blank">配置需求</a>
                <a href="./app/web/index.php?r=admin/index" target="_blank">后台管理</a>
                <a href="./app/web/index.php?r=test/debug&hacker_uid=1" target="blank" style="<?php if (!isset($_GET['hacker_uid']) || $_GET['hacker_uid'] != 1) { echo 'display:none'; } ?>">debug</a>
            </div> 
        </div>
    </div>
    <div style="margin:0 auto;padding-left:100px;width:1000px;margin:100px auto"><a href="../admin.php" target="blank">更多管理请去Discuz!管理中心->应用->安米网手机客户端设置</a></div>
-->
</body>
</html>
