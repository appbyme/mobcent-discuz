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
    <style type="text/css">
        div{
            height: 
        }
    </style>
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
                        <div><span><a href="" data-toggle="modal" data-target=".foundModule" data-backdrop="">编辑</a></span></div>
                    </div>
                    <div class="module">
                        <a href="#"><img title="快速发表" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png" class="img-thumbnail"></a>
                        <div>快速发表</div>
                        <div><span><a href="">编辑</a></span><span></span></div>
                    </div>                       
                    <div class="module">
                        <a href="#"><img title="模块1" src="<?php echo $this->rootUrl; ?>/images/admin/module-default.png" class="img-circle"></a>
                        <div>模块1</div>
                        <div><span><a href="" data-toggle="modal" data-target=".moduleList" data-backdrop="" >编辑</a></span><span><a href="">删除</a></span></div>
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


<!-- 模块编辑 -->
    <div class="modal fade bs-example-modal-lg foundModule" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">发现</h4>
            </div>
            <!-- <form action="index.php" method="post" enctype="multipart/form-data"> -->
            <div class="modal-body">
                <p>
                    <span>编辑名称：</span>
                    <input type="text" value="">
                    <span>请输入1-4个字母、数字或汉字作为名称</span>
                </p>
                <p>
                    <span>编辑图标：</span>
                    <input type="file" value="">
                    <span>请上传1:1比例的JPG或PNG格式图片作为图标</span>
                </p>
                <p>
                    <img src="" style="width:80px;height:80px;border:1px solid blue">
                </p>
<hr>
                <p>
                    <span>页面样式</span>
                    <select id="">
                        <option selected="" value="扁平样式">扁平样式</option>
                        <option value="卡片样式">卡片样式</option>
                    </select>
                </p>

<hr>                
                <div>
                    <p>编辑内容：</p>
                    <div>
                        <div><img src="" style="width:80px;height:80px"></div>
                        <div><span>发表板块：</span></div>
                        <div>
                            <select id="">
                                <option selected="" value="用户自选版块">用户自选版块</option>
                                <option value="版块一">版块一</option>
                                <option value="版块二">版块二</option>
                                <option value="版块三">版块三</option>
                                <option value="版块四">版块四</option>
                              </select>
                              <input type="checkbox" >勾选则需用户填写标题
                              <input type="checkbox" >勾选则显示主题分类
                        </div>
                    </div>
                    <div>
                        <div><img src="" style="width:80px;heigth:80px"></div>
                        <div><span>发表板块：</span></div>
                        <div>
                            <select id="">
                                <option selected="" value="用户自选版块">用户自选版块</option>
                                <option value="版块一">版块一</option>
                                <option value="版块二">版块二</option>
                                <option value="版块三">版块三</option>
                                <option value="版块四">版块四</option>
                              </select>
                              <input type="checkbox" >勾选则需用户填写标题
                              <input type="checkbox" >勾选则显示主题分类
                        </div>
                    </div>
                </div>                

<hr>
            <div>
                <span>选择发表项：</span>
                <select id="">
                    <option selected="" value="发表文字">发表文字</option>
                    <option value="发表图片">发表图片</option>
                    <option value="拍照发表">拍照发表</option>
                    <option value="发表语音">发表语音</option>
                    <option value="签到">签到</option>
                </select>
                <button type="button" class="btn btn-primary">添 加</button>
                <button type="button" class="btn btn-primary">取 消</button>
            </div>

<hr>

            <div>
                <span>模块样式</span>
                <select id="">
                    <option selected="" value="单页面">单页面</option>
                    <option value="二级导航">二级导航</option>
                    <option value="左图右文">左图右文</option>
                    <option value="自定义页面">自定义页面</option>
                </select>                
            </div>

            <div>
                <span>链接地址：</span>
                <select id="u165_input">
                    <option value="版块列表">版块列表</option>
                    <option selected="" value="资讯列表">资讯列表</option>
                    <option value="简版帖子列表">简版帖子列表</option>
                    <option value="消息列表">消息列表</option>
                    <option value="发现">发现</option>
                    <option value="周边用户">周边用户</option>
                    <option value="周边帖子">周边帖子</option>
                    <option value="推荐用户">推荐用户</option>
                    <option value="周边服务">周边服务</option>
                    <option value="设置">设置</option>
                    <option value="关于">关于</option>
                    <option value="外部wap页">外部wap页</option>
                </select>
            </div>

           <div>
                <span>页面样式：</span>
                <select id="u162_input">
                    <option selected="" value="扁平样式">扁平样式</option>
                    <option value="卡片样式">卡片样式</option>
                </select>
           </div> 
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <input type="submit" class="btn btn-primary" value="确定" >  
            </div>


            <!-- </form> -->
        </div>
      </div>
    </div>
<!-- 模块编辑end -->


    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
</body>
</html>