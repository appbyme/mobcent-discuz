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
          padding-top: 40px;
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


        .alert {
            width: 25%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12" align="center">
                <img src="<?php echo $this->rootUrl; ?>/images/logo.png" class="img-responsive img-circle" alt="Responsive image">
            </div>
        </div>
        <form class="form-signin" role="form" action="" method="post">
            <input type="text" name="username" class="form-control" placeholder="请输入用户名" value="<?php echo isset($username) ? $username : '' ?>" required autofocus >
            <input type="password" name="password" class="form-control" placeholder="请输入密码" required >
            <button class="btn btn-primary  btn-lg btn-block" type="submit">登 录</button>
        </form>
    </div>
    
    <div class="alert alert-info fade in hide ">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
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
            setInterval(closeMsg, 3000);
        }
    })

    function closeMsg() {
        $('.alert').alert('close')
    }
</script>

</body>
</html>