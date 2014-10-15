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

    <!-- Static navbar -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">APPbyme</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">网站首页</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">admin <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">退出</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    
    <div id="uidiy-main-view">

    <div class="container">
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
                <div id="module-list">
                    <div class="module last-module">
                        <a href="#" data-toggle="modal" data-target=".module-edit-dlg" data-backdrop="" class="module-add-btn"><img title="模块1" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png" class="img-circle"></a>
                        <div>添加模块</div>
                    </div>
                </div>

                <div id="foot">
                    <p class="text-center">设置完成后请务必点击 
                        <button type="button" class="btn btn-primary btn-sm uidiy-sync-btn">同 步</button> 保证您所添加或设置的内容能在客户端显示！
                    </p> 
                </div>
            </div>
        </div>
    </div>
    
    <div id="module-edit-dlg-view">
    </div>
    <div id="module-remove-dlg-view">
    </div>

    </div>

    <script type="text/javascript">
    var uidiyGlobalObj = {
        rootUrl: '<?php echo $this->rootUrl; ?>',
        moduleInitParams: <?php echo WebUtils::jsonEncode(AppbymeUIDiyModel::initModule()); ?>,
        moduleInitList: <?php echo WebUtils::jsonEncode($modules); ?>,
    };
    </script>
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/underscore-1.7.0.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/backbone-1.1.2.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/admin/uidiy.js"></script>
    <script type="text/template" id="module-template">
    <div class="module" id="module-id-<%= id %>">
        <img title="<%- title %>" src="<%= icon %>" class="img-thumbnail">
        <div><%- title %></div>
        <div>
            <button class="module-edit-btn" data-toggle="modal" data-target=".module-edit-dlg" data-backdrop="">编辑</button>
            <% if (type != '<?php echo AppbymeUIDiyModel::MODULE_TYPE_FASTPOST; ?>' && type != '<?php echo AppbymeUIDiyModel::MODULE_TYPE_DISCOVER; ?>') { %>
            <button class="module-remove-btn" data-toggle="modal" data-target=".module-remove-dlg" data-backdrop="">删除</button>
            <% } %>
        </div>
    </div>
    </script>
    <script type="text/template" id="module-edit-template">
    <div class="modal fade bs-example-modal-lg module-edit-dlg" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title"><%= id != 0 ? '编辑模块' : '添加模块' %></h4>
            </div>
            <form class="module-edit-form form-horizontal">
            <div class="modal-body">

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">编辑名称：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control sm" id="" placeholder="">
                        <p class="help-block">请输入1-4个字母、数字或汉字作为名称</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">编辑图标：</label>
                    <div class="col-sm-10">
                        <input type="file" id="" >
                        <p class="help-block">请上传1:1比例的JPG或PNG格式图片作为图标</p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <img src="" style="width:100px;height:100px;" class="img-rounded">
                    </div>
                </div>

                <% if (type == '<?php echo AppbymeUIDiyModel::MODULE_TYPE_FASTPOST; ?>') { %>
                <div class="edit">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">编辑内容：</label>
                        <div class="col-sm-2">
                            <div class="text-center">
                                <img src="" style="width:80px;height:80px;" class="img-rounded">
                                <p><small>发表文字</small></p>
                            </div>
                        </div>
                        <div class="form-group col-sm-8">
                            <div class="pull-left edit-middle">
                                <label for="" class="">发表版块：</label>
                            </div>
                            <div class="pull-left edit-right">
                                <select class="input-sm">
                                    <option selected="" value="用户自选版块">用户自选版块</option>
                                    <option value="版块一">版块一</option>
                                    <option value="版块二">版块二</option>
                                    <option value="版块三">版块三</option>
                                    <option value="版块四">版块四</option>
                                </select>
                                <div class="checkbox">
                                    <label><input type="checkbox" value=""><small>勾选则需用户填写标题</small></label>
                                </div>                        
                                <div class="checkbox">
                                    <label><input type="checkbox" value=""><small>勾选则显示主题分类</small></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <!-- <label for="" class="col-sm-2 control-label">编辑内容：</label> -->
                        <div class="col-sm-offset-2 col-sm-2">
                            <div class="text-center">
                                <img src="" style="width:80px;height:80px;" class="img-rounded">
                                <p><small>发表文字</small></p>
                            </div>
                        </div>
                        <div class="form-group col-sm-8">
                            <div class="pull-left edit-middle">
                                <label for="" class="">发表版块：</label>
                            </div>
                            <div class="pull-left edit-right">
                                <select class="input-sm">
                                    <option selected="" value="用户自选版块">用户自选版块</option>
                                    <option value="版块一">版块一</option>
                                    <option value="版块二">版块二</option>
                                    <option value="版块三">版块三</option>
                                    <option value="版块四">版块四</option>
                                </select>
                                <div class="checkbox">
                                    <label><input type="checkbox" value=""><small>勾选则需用户填写标题</small></label>
                                </div>                        
                                <div class="checkbox">
                                    <label><input type="checkbox" value=""><small>勾选则显示主题分类</small></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-6">
                            <label for="" class="control-label">选择发表项：</label>
                            <select class="input-sm">
                                <option selected="" value="发表文字">发表文字</option>
                                <option value="发表图片">发表图片</option>
                                <option value="拍照发表">拍照发表</option>
                                <option value="发表语音">发表语音</option>
                                <option value="签到">签到</option>
                            </select>                        
                            <button type="button" class="btn btn-primary btn-sm">添加</button>
                            <button type="button" class="btn btn-primary btn-sm">取消</button>
                        </div>
                    </div>
                </div>
                <% } %>
                <% if (type != '<?php echo AppbymeUIDiyModel::MODULE_TYPE_FASTPOST; ?>' && type != '<?php echo AppbymeUIDiyModel::MODULE_TYPE_DISCOVER; ?>') { %>

                <br>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">模块样式：</label>
                    <div class="col-sm-10">
                    <select class="form-control">
                        <option selected="" value="单页面">单页面</option>
                        <option value="二级导航">二级导航</option>
                        <option value="左图右文">左图右文</option>
                        <option value="自定义页面">自定义页面</option>
                    </select>
                    </div>
                </div>

                <div class="secondary-nav">

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">添加导航：</label>
                        <div class="col-sm-10 secondary-nav-right">
                            <div class="secondary-nav-cate">
                                <div class="pull-left secondary-nav-name">
                                    <small>导航一名称：</small>
                                </div>
                                <div class="pull-left">
                                    <input type="text" class="form-control input-sm">
                                </div>
                            </div>

                            <div class="secondary-nav-cate">
                                <div class="pull-left secondary-nav-name"><small>　链接地址：</small></div>
                                <div class="pull-left">
                                    <select class="form-control input-sm">
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
                                <div class="pull-left option-style">
                                    <div class="forum-list hide" >
                                        <small style="margin:0">设置样式：</small>
                                        <label>
                                            <input type="checkbox" name="" > <small>勾选则显示图标</small>
                                        </label>                                        
                                        <label>
                                            <input type="checkbox" name="" > <small>勾选则双栏显示</small>
                                        </label>
                                    </div>

                                    <div class="consulting-list hide">
                                        <div class="pull-left secondary-nav-name"><small>　选择门户：</small></div>
                                        <div class="pull-left">
                                            <select class="form-control input-sm">
                                            <option selected="" value="资讯模块一">资讯模块一</option>
                                            <option value="资讯模块二">资讯模块二</option>
                                            <option value="资讯模块三">资讯模块三</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="simple-topic-list hide">
                                        <div class="pull-left secondary-nav-name"><small>　选择版块：</small></div>
                                        <div class="pull-left">
                                            <select class="form-control input-sm">
                                                <option selected="" value="论坛版块一">论坛版块一</option>
                                                <option value="论坛版块二">论坛版块二</option>
                                                <option value="论坛版块三">论坛版块三</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="wap-link">
                                        <div class="pull-left secondary-nav-name">
                                            <small>wap地址：</small>
                                        </div>
                                        <div class="pull-left">
                                            <input type="text" class="form-control input-sm">
                                        </div>
                                    </div>
                                </div> 

                                <div class="show-style">
                                    <div class="pull-left secondary-nav-name"><small>　链接地址：</small></div>
                                    <div class="pull-left">
                                        <select class="form-control input-sm">
                                            <option selected="" value="扁平样式">扁平样式</option>
                                            <option value="卡片样式">卡片样式</option>
                                        </select>
                                    </div>
                                </div>

                            </div> <!-- end secondary-nav-cate 二级栏目导航 -->

                        </div>
                    </div>

                    <div class="form-group">
                        <!-- <label for="" class="col-sm-2 control-label">添加导航：</label> -->
                        <div class="col-sm-offset-2 col-sm-10 secondary-nav-right">
                            <div class="secondary-nav-cate">
                                <div class="pull-left secondary-nav-name">
                                    <small>导航一名称：</small>
                                </div>
                                <div class="pull-left">
                                    <input type="text" class="form-control input-sm">
                                </div>
                            </div>

                            <div class="secondary-nav-cate">
                                <div class="pull-left secondary-nav-name"><small>　链接地址：</small></div>
                                <div class="pull-left">
                                    <select class="form-control input-sm">
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
                                <div class="pull-left option-style">
                                    <div class="forum-list hide" >
                                        <small style="margin:0">设置样式：</small>
                                        <label>
                                            <input type="checkbox" name="" > <small>勾选则显示图标</small>
                                        </label>                                        
                                        <label>
                                            <input type="checkbox" name="" > <small>勾选则双栏显示</small>
                                        </label>
                                    </div>

                                    <div class="consulting-list hide">
                                        <div class="pull-left secondary-nav-name"><small>　选择门户：</small></div>
                                        <div class="pull-left">
                                            <select class="form-control input-sm">
                                            <option selected="" value="资讯模块一">资讯模块一</option>
                                            <option value="资讯模块二">资讯模块二</option>
                                            <option value="资讯模块三">资讯模块三</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="simple-topic-list hide">
                                        <div class="pull-left secondary-nav-name"><small>　选择版块：</small></div>
                                        <div class="pull-left">
                                            <select class="form-control input-sm">
                                                <option selected="" value="论坛版块一">论坛版块一</option>
                                                <option value="论坛版块二">论坛版块二</option>
                                                <option value="论坛版块三">论坛版块三</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="wap-link">
                                        <div class="pull-left secondary-nav-name">
                                            <small>wap地址：</small>
                                        </div>
                                        <div class="pull-left">
                                            <input type="text" class="form-control input-sm">
                                        </div>
                                    </div>
                                </div> 

                                <div class="show-style">
                                    <div class="pull-left secondary-nav-name"><small>　链接地址：</small></div>
                                    <div class="pull-left">
                                        <select class="form-control input-sm">
                                            <option selected="" value="扁平样式">扁平样式</option>
                                            <option value="卡片样式">卡片样式</option>
                                        </select>
                                    </div>
                                </div>

                            </div> <!-- end secondary-nav-cate 二级栏目导航 -->

                        </div>
                    </div>

                </div> <!-- end secondary-nav -->


                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">链接地址：</label>
                    <div class="col-sm-3">
                        <select class="form-control">
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
                    <div class="col-sm-5">
                        <div class="forum-list hide" >
                            <small style="margin:0">设置样式：</small>
                            <label>
                                <input type="checkbox" name="" > <small>勾选则显示图标</small>
                            </label>                                        
                            <label>
                                <input type="checkbox" name="" > <small>勾选则双栏显示</small>
                            </label>
                        </div>

                        <div class="simple-topic-list">
                            <div class="pull-left secondary-nav-name"><small>　选择版块：</small></div>
                            <div class="pull-left">
                                <select class="form-control input-sm">
                                    <option selected="" value="论坛版块一">论坛版块一</option>
                                    <option value="论坛版块二">论坛版块二</option>
                                    <option value="论坛版块三">论坛版块三</option>
                                </select>
                            </div>
                        </div>

                        <div class="wap-link hide">
                            <div class="pull-left secondary-nav-name">
                                <small>wap地址：</small>
                            </div>
                            <div class="pull-left">
                                <input type="text" class="form-control input-sm">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">页面样式：</label>
                    <div class="col-sm-10">
                        <select class="form-control">
                            <option selected="" value="扁平样式">扁平样式</option>
                            <option value="卡片样式">卡片样式</option>
                        </select> 
                    </div>
                </div>



                <% } %>    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <input type="submit" class="btn btn-primary" value="确定" >  
            </div>
            </form>
        </div>
      </div>
    </div>
    </script>
    <script type="text/template" id="module-remove-template">
    <div class="modal fade bs-example-modal-sm module-remove-dlg" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title"><%= '删除模块' %></h4>
            </div>
            <form class="module-remove-form">
            <div class="modal-body">
                <h5>是否要删除 <%- title %> 模块</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>
                <input type="submit" class="btn btn-primary" value="确定" >  
            </div>
            </form>
        </div>
      </div>
    </div>
    </script>
</body>
</html>