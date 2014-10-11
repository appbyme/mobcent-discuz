<!DOCTYPE html>
<html>
<head>
    <title>安米后台管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-theme-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy.css">
</head>
<body>
    <div class="container" id="main">
        <div class="row">

            <div class="col-md-4">
                <div id="mobleShow"></div>
            </div>

            <div class="col-md-8" id="operation">
                <p class="navCategory">选择导航样式</p>
                <div id="footNav">
                    <label><input type="radio"> 底部导航</label>
                </div>

                <p class="navCategory">模块管理</p>
                <div id="moduleManage">
                    <div class="module">
                        <a href="#"><img title="发现" src="<?php echo $this->rootUrl; ?>/images/admin/module-default.png" class="img-thumbnail"></a>
                        <div>发现</div>
                        <div><span><a href="" data-toggle="modal" data-target=".bs-example-modal-lg" data-backdrop="">编辑</a></span></div>
                    </div>
                    <div class="module">
                        <a href="#"><img title="快速发表" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png" class="img-thumbnail"></a>
                        <div>快速发表</div>
                        <div><span><a href="">编辑</a></span><span></span></div>
                    </div>                       
                    <div class="module">
                        <a href="#"><img title="模块1" src="<?php echo $this->rootUrl; ?>/images/admin/module-default.png" class="img-circle"></a>
                        <div>模块1</div>
                        <div><span><a href="">编辑</a></span><span><a href="">删除</a></span></div>
                    </div>
                    <div class="module">
                        <a href="#"><img title="模块1" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png" class="img-circle"></a>
                        <div>添加模块</div>
                    </div>
                </div>

                <div id="foot">
                    <p class="text-center">设置完成后请务必点击 <button type="button" class="btn btn-primary btn-sm">同 步</button> 保证您所添加或设置的内容能在客户端显示！</p>
                </div>
            </div>
        </div>
    </div>


<!-- 发现模块编辑 -->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">发现</h4>
            </div>
            <form action="index.php" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                <div>
                    <p>频道名称：<input type="text" value="发现" ></p>
                    <p>请输入1-4个字母、数字或汉字作为频道名称</p>
                    <p>频道图标：<input type="file"></p>
                    <p>请上传比例为1:1的JPG或PNG格式的图片</p>
                    <p><img src=""></p>
                    <p>
                        <select>
                            <option value="">扁平样式</option>
                            <option value="">卡片样式</option>
                        </select>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <input type="submit" class="btn btn-primary" value="确定" >  
            </div>
            </form>
        </div>
      </div>
    </div>
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
</body>
</html>