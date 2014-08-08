<?php header("Content-Type: text/html; charset=utf-8");?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
    <table>
<?php
foreach ($testApiList as $api) {
    printf('<tr><td><a href="%s" target="_blank">%s</td></tr>', 
        WebUtils::createUrl_oldVersion($api['route'], array_merge($api['params'], $_GET)),
        $api['title']
    );
}
?>
    </table>  
</body>
</html>