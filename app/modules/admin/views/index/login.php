<!DOCTYPE html>
<html>
<head>
    <title>login</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-theme-3.2.0.min.css">
</head>
<body>
    <div class="container">
      <h2>Login</h2>
      <form class="form-horizontal" role="form" method="post">
        <div class="form-group">
          <label class="control-label col-sm-2" for="username">user:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-2" for="password">Password:</label>
          <div class="col-sm-10">          
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
          </div>
        </div>
        <div class="form-group">        
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Submit</button>
          </div>
        </div>
      </form>
    </div>

    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
</body>
</html>