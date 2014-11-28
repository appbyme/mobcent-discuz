<!DOCTYPE html>
<html>
<head>
    <title>登录安米后台管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-theme-3.2.0.min.css">
    <style type="text/css">
        body {
          /*padding-top: 40px;*/
          padding-bottom: 40px;
          /*background-color: #eee;*/
        }

        .form-signin {
          max-width: 335px;
          padding: 10px;
          margin: 0 auto;
        }
        .form-signin .form-signin-heading,
        .form-signin .checkbox {
          margin-bottom: 10px;
        }

        .form-signin .form-control {
          position: relative;
          height: auto;
          -webkit-box-sizing: border-box;
             -moz-box-sizing: border-box;
                  box-sizing: border-box;
          padding: 10px;
          font-size: 16px;
        }

        .form-signin input[type="text"] {
          margin-bottom: 5px;
/*          border-bottom-right-radius: 0;
          border-bottom-left-radius: 0;*/
        }
        .form-signin input[type="password"] {
          margin-bottom: 10px;
/*          border-top-left-radius: 0;
          border-top-right-radius: 0;*/
        }      
        #login{
            width: 300px;
            height: 60px;
            background-image:url(./images/logo.png);
            background-position:50% 50%;
        }  

        .login-failure {
            width: 25%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="alert alert-info text-center"><a href="#" class="close" data-dismiss="alert">&times;</a>
   <strong>提示！</strong>请使用Discuz!的管理员账号，或者是安米插件配置允许的用户进行登录。
</div>
    <div class="container" style="margin-top:10px;">
        <div class="row">
            <div class="col-md-12" align="center">
            <a href="http://www.appbyme.com"><img src="<?php echo $this->rootUrl; ?>/images/logo.png" class="img-responsive img-circle"></a>
            </div>
        </div>
        <form class="form-signin" role="form" action="" method="post" autocomplete="off">
            <input type="text" name="username" class="form-control" placeholder="请输入用户名" required autofocus >
            <input type="password" name="password" class="form-control" placeholder="请输入密码" autocomplete="off" required >
            <button class="btn btn-primary  btn-lg btn-block" type="submit">登 录</button>
        </form>
    </div>

<div class="alert alert-info fade in hide login-failure"><a href="#" class="close" data-dismiss="alert">&times;</a>
    <strong>登录失败！</strong><p>您的网络连接有问题。</p>
</div>
<script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
<script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
<script>
    $(function(){
        var errorMsg = '<?php echo $errorMsg; ?>';

        if (errorMsg != '') {
            $('.alert p').html(errorMsg);
            $('.alert').removeClass('hide');
            setTimeout(closeMsg, 3000);
        }
    })

    function closeMsg() {
        $('.alert').alert('close')
    }
    
    setTimeout("$('.alert-info').fadeOut(18000);", 1000);
</script>

</body>
</html>