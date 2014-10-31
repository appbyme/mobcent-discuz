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
        .mobleShow {
            width: 380px;
            background:url("<?php echo $this->rootUrl; ?>/images/admin/moble.png") no-repeat right top;
            text-align: center;
        }
    </style>
</head>
<body>


<div class="covering"></div>

<?php global $_G; ?>
    <!-- Static navbar -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">APPbyme</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav nav-list">
            <li class=""><a href="<?php echo $this->dzRootUrl; ?>" target="_blank">网站首页</a></li>
            <li><a href="http://addon.discuz.com/?@appbyme_app.plugin.doc/install" target="_blank">说明文档</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/plugin.php?id=appbyme_app:download" target="_blank">应用下载</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/requirements/index.php" target="_blank">配置需求</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/admin.php" target="_blank">Discuz!管理中心</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_G['username']; ?> <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="<?php echo Yii::app()->createAbsoluteUrl('admin/index/logout'); ?>">退出</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    
    <div id="uidiy-main-view">

    <div class="container" style="width:1200px;height:800px;">
        <div class="row">

            <div class="col-sm-xs-4 col-sm-4 col-md-4">
                <div class="mobleShow">

                    <div class="moble-content"> 

                        <div id="navitem-edit-dlg-view" class="play-border-add">
                        </div>
                        <div id="navitem-remove-dlg-view" class="play-border-add">
                        </div>

                        <!-- 发现下方加号弹出框 -->
                        <div id="module-topbar-dlg-view" class="play-add-plug">
                        </div>

                        <!-- 左图右文添加/编辑弹出框 -->
                        <div id="news-component-edit-dlg-view" class="pic-text-pop">
                        </div>

                        <!-- 添加风格区弹出框 -->
                        <div class="add-style-pop">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title pull-left">添加风格区</h3>
                                    <button type="button" class="close close-style-pop pull-right">&times;</button>
                                </div>
                                <form class="form-horizontal navitem-edit-form">
                                    <div class="panel-body">
                                        <form class="form-horizontal">

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">选择风格区: </label>
                                                <div class="col-sm-4">
                                                    <select class="form-control input-sm">
                                                        <option >默认风格</option>
                                                        <option >图片墙风格</option>
                                                        <option >九宫格风格</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">是否显示标题：</label>
                                                <div class="col-sm-8 text-left">
                                                    <!-- 没加name -->
                                                    <label class="radio-inline"><input type="radio"> 是</label>
                                                    <label class="radio-inline"><input type="radio"> 否</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">风格区标题：</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm">
                                                    <p class="help-block">输入1-9个汉字、数字或字母</p>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">标题位置：</label>
                                                <div class="col-sm-8" style="padding:0px 0px 0px 15px;">
                                                    <label class="radio-inline pull-left"><input type="radio"> 风格区顶部</label>
                                                    <label class="radio-inline pull-left"><input type="radio"> 风格区底部</label>
                                                </div>
                                            </div>

                                        </form> 
                                    </div>
                                </form>
                                <div class="panel-footer text-right">
                                    <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
                                    <button type="button" class="btn btn-default btn-sm close-style-pop">取 消</button>
                                </div>
                            </div>
                        </div>

                        <!-- 添加组件弹出框 -->
                        <div class="add-comp-pop">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title pull-left">添加组件</h3>
                                    <button type="button" class="close close-comp-pop pull-right">&times;</button>
                                </div>
                                <form class="form-horizontal navitem-edit-form">
                                    <div class="panel-body">
                                        <form class="form-horizontal">

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">选择视窗类型: </label>
                                                <div class="col-sm-4">
                                                    <select class="form-control input-sm">
                                                        <option value="">单栏样式(高)</option>
                                                        <option value="">单栏样式(中)</option>
                                                        <option value="">单栏样式(低)</option>
                                                        <option value="">双栏样式(高)</option>
                                                        <option value="">双栏样式(中)</option>
                                                        <option value="">双栏样式(低)</option>
                                                        <option value="">三栏样式(高)</option>
                                                        <option value="">三栏样式(中)</option>
                                                        <option value="">三栏样式(低)</option>
                                                        <option value="">1+2样式</option>
                                                        <option value="">2+1样式</option>
                                                        <option value="">1+3样式</option>
                                                        <option value="">3+1样式</option>
                                                        <option value="">上1下2样式</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </form> 
                                    </div>
                                </form>
                                <div class="panel-footer text-right">
                                    <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
                                    <button type="button" class="btn btn-default btn-sm close-comp-pop">取 消</button>
                                </div>
                            </div>
                        </div>

                        <img class="hidden" src="<?php echo $this->rootUrl; ?>/images/admin/moble-bg.png">

                        <div id="module-edit-mobile-view">
                        </div>

                        <!-- 添加风格 -->
                        <div class="add-style hidden">
						    <div class="single-style">
						    	<div class="style-content">
						    		<div class="style-title">
						    			<p class="pull-left">此处是风格区标题</p>
						    			<a class="pull-right" href="javascript:void()">更多</a>
						    		</div>
						    		<div class="style-area">
						    			分割区示意
						    		</div>
						    		<div class="style-area">
										分割区示意
						    		</div>
						    		<div class="style-title">
						    			<p class="pull-left">此处是风格区标题</p>
						    			<a class="pull-right" href="javascript:void()">更多</a>
						    		</div>
						    	</div>
						    	<button type="button" class="btn btn-primary btn-xs">继续添加组件</button>
						    	<button type="button" class="btn btn-primary btn-xs">删除该风格区</button>
						    </div>

                            <button type="button" class="btn btn-primary add-style-btn close-style-pop hidden">点击添加风格区</button>
                            <button type="button" class="btn btn-primary add-style-btn close-style-pop">继续添加风格区</button>
                            <button type="button" class="btn btn-primary add-style-comp close-comp-pop">点击添加组件</button>
                        </div>

                        <!-- 手机底部导航 -->
                        <div class="moble-bottom-nav">
                            <div class="nav-move">
                                <div class="pull-left nav-add navitem-add-btn">
                                    <img src="<?php echo $this->rootUrl; ?>/images/admin/add-nav-ico.png">
                                </div>
                            </div>
                        </div>

                    </div><!-- end moble-content -->

                </div>
            </div>

            <div class="col-sm-xs-8 col-sm-8 col-md-8" id="operation">

                <div id="module-edit-dlg-view">
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">选择导航样式</h3>
                    </div>
                    <div class="panel-body">
                        <div class="radio">
                            <label><input type="radio"> 底部导航</label>
                        </div>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">模块管理</h3>
                    </div>
                    <div class="panel-body module-management">
                        <div id="module-list">
                            <div class="module last-module">
                                <a href="#" data-toggle="modal" class="module-add-btn"><img title="模块1" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png" class="img-circle"></a>
                                <div>添加模块</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="foot">
                    <p class="text-center">
                        设置完成后请务必点击 
                        <button type="button" class="btn btn-primary btn-sm uidiy-sync-btn">同 步</button> 保证您所添加或设置的内容能在客户端显示！
                        恢复初始设置可以点击
                        <button type="button" class="btn btn-primary btn-sm uidiy-init-btn">默认设置</button> 来进行恢复！
                    </p>                     
                </div>
            </div>
        </div>
    </div>
    
    <div id="module-remove-dlg-view">
    </div>

    </div>

    <script type="text/javascript">
    var uidiyGlobalObj = {
        rootUrl: '<?php echo $this->rootUrl; ?>',
        moduleInitParams: <?php echo WebUtils::jsonEncode(AppbymeUIDiyModel::initModule(), 'utf-8'); ?>,
        componentInitParams: <?php echo WebUtils::jsonEncode(AppbymeUIDiyModel::initComponent(), 'utf-8'); ?>,
        moduleInitList: <?php echo WebUtils::jsonEncode($modules, 'utf-8'); ?>,
        navItemInitParams: <?php echo WebUtils::jsonEncode(AppbymeUIDiyModel::initNavItem(), 'utf-8'); ?>,
        navItemInitList: <?php echo WebUtils::jsonEncode($navInfo['navItemList'], 'utf-8'); ?>,
    };
    <?php
    $reflect = new ReflectionClass('AppbymeUIDiyModel');
    foreach ($reflect->getConstants() as $key => $value) {
        echo "var {$key} = '$value';";
    }
    ?>
    var SUBNAV_MAX_COMPONENT_LEN = 4;
    </script>
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/underscore-1.7.0.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/backbone-1.1.2.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/admin/uidiy.js"></script>
    <script type="text/javascript" src="<?php echo $this->rootUrl; ?>/js/jquery-ui-1.11.2.min.js"></script>
    <!-- 底部导航模板 -->
    <script type="text/template" id="navitem-template">
    <div class="pull-left nav-column" style='background:url("<?php echo $this->rootUrl; ?>/images/admin/<%= icon %>.png") no-repeat 50% 20%'>
        <small class="navitem-title"><%= title %></small>
        <% if (moduleId != MODULE_ID_DISCOVER) { %>
        <div class="nav-edit hidden">
            <a><span class="navitem-edit-btn"><small>编辑</a></small></span></a>
            <a><span class="navitem-remove-btn"><small>删除</small></span></a>
        </div>
        <% } %>
    </div>
    </script>
    <!-- topbar 编辑模板 -->
    <script type="text/template" id="module-topbar-dlg-template">
    <div class="panel panel-primary">
        <form class="module-topbar-edit-form">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">插件设置</h3>
            <button type="button" class="close close-topbar-btn pull-right">&times;</button>
        </div>
        <input type="hidden" name="topbarIndex" id="topbarIndex" value="0">
        <div class="panel-body" style="padding:25px 0px">
            <label class="radio-inline">
                <input type="radio" name="topbarComponentType" value="<%= COMPONENT_TYPE_DEFAULT %>" <%= type == COMPONENT_TYPE_DEFAULT ? 'checked' : '' %>> 取消
            </label>
            <label class="radio-inline">
                <input type="radio" name="topbarComponentType" value="<%= COMPONENT_TYPE_WEATHER %>" <%= type == COMPONENT_TYPE_WEATHER ? 'checked' : '' %>> 天气
            </label>
            <label class="radio-inline">
                <input type="radio" name="topbarComponentType" value="<%= COMPONENT_TYPE_USERINFO %>" <%= type == COMPONENT_TYPE_USERINFO ? 'checked' : '' %>> 用户中心
            </label>
            <label class="radio-inline">
                <input type="radio" name="topbarComponentType" value="<%= COMPONENT_TYPE_SIGN %>" <%= type == COMPONENT_TYPE_SIGN ? 'checked' : '' %>> 签到
            </label>
            <label class="radio-inline">
                <input type="radio" name="topbarComponentType" value="<%= COMPONENT_TYPE_SEARCH %>" <%= type == COMPONENT_TYPE_SEARCH ? 'checked' : '' %>> 搜索
            </label>
        </div>
        <div class="panel-footer text-right">
            <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
            <button type="button" class="btn btn-default btn-sm close-topbar-btn">取 消</button>
        </div>
        </form>
    </div>
    </script>
    <!-- 导航添加/编辑模板 -->
    <script type="text/template" id="navitem-edit-template">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title pull-left"><%= moduleId == 0 ? '添加导航' : '编辑导航' %></h3>
            <button type="button" class="add-nav-close close pull-right">&times;</button>
        </div>
        <form class="form-horizontal navitem-edit-form">
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-4 control-label">导航名字：</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="navItemTitle" value="<%= title %>">
                    <p class="help-block">输入1-4个字母、数字或汉字</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">导航图标：</label>
                <div class="col-sm-4">
                    <button type="button" class="btn btn-primary">选择图标</button>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4 text-left">
                    <img src="" style="width:60px;height:60px;" class="img-rounded">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">链接地址: </label>
                <div class="col-sm-4">
                    <select name="navItemModuleId" class="form-control">
                    <% for (var i = 0; i < Appbyme.uiModules.models.length; i++) {
                        var module = Appbyme.uiModules.models[i]; 
                        if (module.id != MODULE_ID_DISCOVER) {
                    %>
                        <option value="<%= module.id %>" <%= moduleId == module.id ? 'selected' : '' %>><%= module.attributes.title %></option>
                    <% }} %>
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
            <button type="button" class="btn btn-default btn-sm add-nav-close">取 消</button>
        </div>
        </form>
    </div>
    </script>
    <!-- 导航删除模板 -->
    <script type="text/template" id="navitem-remove-template">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">删除导航</h3>
            <button type="button" class="btn-remove-navitem close pull-right">&times;</button>
        </div>
        <form class="form-horizontal navitem-remove-form">
        <div class="panel-body">
            确定要删除 <%= title %> 导航吗?
        </div>
        <div class="panel-footer text-right">
            <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
            <button type="button" class="btn btn-default btn-sm btn-remove-navitem">取 消</button>
        </div>
        </form>
    </div>
    </script>
    <script type="text/template" id="module-template">
    <div class="module" id="module-id-<%= id %>">
        <img title="<%- title %>" src="<%= icon %>" class="img-thumbnail">
        <div class="module-title"><%- title %></div>
        <div>
            <button class="module-edit-btn btn btn-primary btn-xs">编 辑</button>
            <% if (id != MODULE_ID_FASTPOST && id != MODULE_ID_DISCOVER) { %>
            <button class="module-remove-btn btn btn-primary btn-xs" data-toggle="modal" data-target=".module-remove-dlg" data-backdrop="">删 除</button>
            <% } %>
        </div>
    </div>
    </script>
    <script type="text/template" id="module-edit-template">
    <div class="module-play">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title pull-left"><%= id != 0 ? '编辑模块' : '添加模块' %></h3>
                <button type="button" class="close close-module-play pull-right">&times;</button>
            </div>
            <form class="module-edit-form form-horizontal">
            <div class="panel-body">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">模块名称：</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control sm" name="moduleTitle" value="<%- title %>" placeholder="">
                            <p class="help-block">请输入1-4个字母、数字或汉字作为名称</p>
                        </div>
                    </div>

                    <div class="form-group hidden">
                        <label for="" class="col-sm-2 control-label">编辑图标：</label>
                        <div class="col-sm-10">
                            <input type="file" id="" >
                            <p class="help-block">请上传1:1比例的JPG或PNG格式图片作为图标</p>
                        </div>
                    </div>

                    <div class="form-group hidden">
                        <div class="col-sm-offset-2 col-sm-10">
                            <img src="" style="width:100px;height:100px;" class="img-rounded">
                        </div>
                    </div>

                    <% var isModuleTypeSelect = id != MODULE_ID_FASTPOST && id != MODULE_ID_DISCOVER; %>
                    <div class="<%= !isModuleTypeSelect ? 'hidden' : '' %> form-group">
                        <label class="col-sm-2 control-label">模块样式: </label>
                        <div class="col-sm-10">
                        <select id="moduleType" name="moduleType" class="form-control">
                            <option value="<%= MODULE_TYPE_FASTPOST %>" <%= type == MODULE_TYPE_FASTPOST ? 'selected' : '' %> class="<%= isModuleTypeSelect ? 'hidden' : '' %>">快速发帖</option>
                            <option value="<%= MODULE_TYPE_FULL %>" <%= type == MODULE_TYPE_FULL ? 'selected' : '' %>>单页面</option>
                            <option value="<%= MODULE_TYPE_SUBNAV %>" <%= type == MODULE_TYPE_SUBNAV ? 'selected' : '' %>>二级导航</option>
                            <option value="<%= MODULE_TYPE_NEWS %>" <%= type == MODULE_TYPE_NEWS ? 'selected' : '' %>>左图右文</option>
                            <option value="<%= MODULE_TYPE_CUSTOM %>" <%= type == MODULE_TYPE_CUSTOM ? 'selected' : '' %>>自定义页面</option>
                        </select>
                        </div>
                    </div>

                    <div id="module-edit-detail-view">
                    </div>

            </div>
            <div class="panel-footer text-right">
                <input type="submit" class="btn btn-primary" value="确定" >  
                <button type="button" class="btn btn-default close-module-play">取 消</button>
            </div>
            </form>
        </div>
    </div>
    </script>
    <!-- 模块于手机内的编辑模板 -->
    <script type="text/template" id="module-edit-mobile-template">
    <img class="moble-top-show" src="<?php echo $this->rootUrl; ?>/images/admin/moble-nav.png">
    <div class="moble-top-title">
        <img class="pull-left select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <span><%= title %></span>
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
    </div>
    <% if (id == MODULE_ID_DISCOVER) { %>
    <div class="found-module" style="background:;height:450px;">
        <div class="slide-img">
            <img src="<?php echo $this->rootUrl; ?>/images/admin/timo.jpg">
            <span><a href="">点击添加更多幻灯片</a></span>
        </div>
        <div class="module-show">
            <div class="module-show-one"><img class="pull-left" src="<?php echo $this->rootUrl; ?>/images/admin/moble-ico.jpg">
                <span class="pull-left ">个人中心</span><span class="pull-right"><small><a href="">隐藏</a></small></span>
            </div>
            <div class="module-show-two">
                <img class="pull-left" src="<?php echo $this->rootUrl; ?>/images/admin/moble-ico.jpg">
                <span class="pull-left">设置</span><span class="pull-right"><small><a href="">显示</a></small></span>
            </div>
            <div class="module-show-three">
                <img class="pull-left" src="<?php echo $this->rootUrl; ?>/images/admin/moble-ico.jpg">
                <span class="pull-left">关于</span><span class="pull-right"><small><a href="">显示</a></small></span>
            </div>
        </div>

        <div class="module-show">
            <div class="module-show-one"><img class="pull-left" src="<?php echo $this->rootUrl; ?>/images/admin/moble-ico.jpg">
                <span class="pull-left ">周边生活</span>
            </div>
            <div class="module-show-two">
                <img class="pull-left" src="<?php echo $this->rootUrl; ?>/images/admin/moble-ico.jpg">
                <span class="pull-left">商城</span>
            </div>
            <div class="module-show-three">
                <img class="pull-left" src="<?php echo $this->rootUrl; ?>/images/admin/moble-ico.jpg">
                <span class="pull-left">活动专区</span>
            </div>
        </div>

        <div class="module-show">
            <div class="module-show-two">
                <img class="pull-left" src="<?php echo $this->rootUrl; ?>/images/admin/moble-ico.jpg">
                <span class="pull-left">商城</span>
                <span class="pull-right"><small><a href="">编辑</a></small></span>
                <span class="pull-right"><small><a href="">删除</a></small></span>
            </div>
            <div class="module-show-three">
                <img class="pull-left" src="<?php echo $this->rootUrl; ?>/images/admin/moble-ico.jpg">
                <span class="pull-left">活动专区</span>
                <span class="pull-right"><small><a href="">编辑</a></small></span>
                <span class="pull-right"><small><a href="">删除</a></small></span>
            </div>
        </div>
        <span><a href="">点击添加更多</a></span>
    </div>
    <% } else if (id == MODULE_ID_FASTPOST) { %>
    <% } else if (type == MODULE_TYPE_FULL) { %>
    <% } else if (type == MODULE_TYPE_SUBNAV) { %>
    <% } else if (type == MODULE_TYPE_NEWS) { %>
    <!-- 左图右文 -->
    <div class="pic-text">
        <div class="news-component-item-container">
        </div>
        <div class="text-center">
           <button type="button" class="btn btn-primary add-news-component-item-btn">点击添加更多</button>
        </div>
    </div>
    <% } else if (type == MODULE_TYPE_CUSTOM) { %>
    <% } %> 
    </script>
    <script type="text/template" id="module-edit-detail-template">
    <% if (id == MODULE_ID_DISCOVER) { %>
    <% } else if (id == MODULE_ID_FASTPOST) { %>

    <div class="edit">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">编辑内容：</label>
        </div>
        <div class="fastpost-components-container">
        </div>
        <div class="form-group">
            <button class="more-fastpost-btn btn btn-primary btn-sm">点击添加更多发表项</button>
        <div>
        <div class="form-group fastpost-item-select-div hidden">
            <div class="col-sm-offset-2 col-sm-7">
                <label for="" class="control-label">选择发表项：</label>
                <select class="input-sm" name="fastpostItemSelect">
                    <option value="<%= COMPONENT_TYPE_FASTTEXT %>">发表文字</option>
                    <option value="<%= COMPONENT_TYPE_FASTIMAGE %>">发表图片</option>
                    <option value="<%= COMPONENT_TYPE_FASTCAMERA %>">拍照发表</option>
                    <option value="<%= COMPONENT_TYPE_FASTAUDIO %>">发表语音</option>
                    <option value="<%= COMPONENT_TYPE_SIGN %>">签到</option>
                </select>                        
                <button type="button" class="btn btn-primary btn-sm add-fastpost-item-btn">添加</button>
                <button type="button" class="btn btn-primary btn-sm close-fastpost-item-btn">取消</button>
            </div>
        </div>
    </div>

    <% } else if (type == MODULE_TYPE_FULL) { %>
    <div class="component-view-container"></div>
    <% } else if (type == MODULE_TYPE_SUBNAV) { %>
    <div><label>添加导航: </label></div>
    <div class="add-nav-list">
        <div class="component-view-container"></div>
        <div class="component-view-container"></div>
        <div class="component-view-container"></div>
        <div class="component-view-container"></div>
    </div>
    <% } else if (type == MODULE_TYPE_NEWS) { %>
        <div class="col-sm-offset-2 col-sm-4">
            <h5>请在左侧预览图中设置添加内容</h5>
        </div>
    <% } else if (type == MODULE_TYPE_CUSTOM) { %>
        <div class="col-sm-offset-2 col-sm-4">
            <h5>请在左侧预览图中设置添加内容</h5>
        </div>
    <% } %> 
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
                <h4 class="modal-title">删除模块</h4>
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
    <script type="text/template" id="component-template">
    <div class="component-view quick-edit" id="component-view-<%= id %>">

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">导航名称：</label>
            <div class="col-sm-10">
                <input type="text" class="form-control input-sm" name="componentTitle[]" value="<%= title %>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">内容简介：</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="componentDesc[]" rows="3" style="resize:none;margin-bottom:8px;"><%= desc %></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">链接地址：</label>
            <div class="col-sm-10">
            <select name="componentType[]" class="selectComponentType form-control">
                <option value="<%= COMPONENT_TYPE_FORUMLIST %>" <%= type == COMPONENT_TYPE_FORUMLIST ? 'selected' : '' %>>版块列表</option>
                <option value="<%= COMPONENT_TYPE_NEWSLIST %>" <%= type == COMPONENT_TYPE_NEWSLIST ? 'selected' : '' %>>资讯列表</option>
                <option value="<%= COMPONENT_TYPE_TOPICLIST %>" <%= type == COMPONENT_TYPE_TOPICLIST ? 'selected' : '' %>>简版帖子列表</option>
                <option value="<%= COMPONENT_TYPE_MESSAGELIST %>" <%= type == COMPONENT_TYPE_MESSAGELIST ? 'selected' : '' %>>消息列表</option>
                <option value="<%= COMPONENT_TYPE_SURROUDING_USERLIST %>" <%= type == COMPONENT_TYPE_SURROUDING_USERLIST ? 'selected' : '' %>>周边用户</option>
                <option value="<%= COMPONENT_TYPE_SURROUDING_POSTLIST %>" <%= type == COMPONENT_TYPE_SURROUDING_POSTLIST ? 'selected' : '' %>>周边帖子</option>
                <option value="<%= COMPONENT_TYPE_RECOMMEND_USERLIST %>" <%= type == COMPONENT_TYPE_RECOMMEND_USERLIST ? 'selected' : '' %>>推荐用户</option>
                <option value="<%= COMPONENT_TYPE_SETTING %>" <%= type == COMPONENT_TYPE_SETTING ? 'selected' : '' %>>设置</option>
                <option value="<%= COMPONENT_TYPE_ABOAT %>" <%= type == COMPONENT_TYPE_ABOAT ? 'selected' : '' %>>关于</option>
                <option value="<%= COMPONENT_TYPE_WEBAPP %>" <%= type == COMPONENT_TYPE_WEBAPP ? 'selected' : '' %>>外部wap页</option>
                <option value="<%= COMPONENT_TYPE_FASTTEXT %>" <%= type == COMPONENT_TYPE_FASTTEXT ? 'selected' : '' %>>发表文字</option>
                <option value="<%= COMPONENT_TYPE_FASTIMAGE %>" <%= type == COMPONENT_TYPE_FASTIMAGE ? 'selected' : '' %>>发表图片</option>
                <option value="<%= COMPONENT_TYPE_FASTCAMERA %>" <%= type == COMPONENT_TYPE_FASTCAMERA ? 'selected' : '' %>>拍照发表</option>
                <option value="<%= COMPONENT_TYPE_FASTAUDIO %>" <%= type == COMPONENT_TYPE_FASTAUDIO ? 'selected' : '' %>>发表语音</option>
                <option value="<%= COMPONENT_TYPE_SIGN %>" <%= type == COMPONENT_TYPE_SIGN ? 'selected' : '' %>>签到</option>
            </select>
            </div>
        </div>

        <div id="component-view-<% print(COMPONENT_TYPE_FORUMLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_FORUMLIST ? '' : 'hidden' %>">

            <div class="form-group">
                <label class="col-sm-2 control-label">设置样式：</label>
                <div class="col-sm-10">
                    <div>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="isShowForumIcon[]" <%= extParams.isShowForumIcon ? 'checked' : '' %>><small>勾选则显示图标</small>
                    </label>
                    </div>
                    <div>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="isShowForumTwoCols[]" <%= extParams.isShowForumTwoCols ? 'checked' : '' %>> <small>勾选则双栏显示</small>
                    </label>
                    </div>
                </div>
            </div>


        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_NEWSLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_NEWSLIST ? '' : 'hidden' %>">

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">选择门户：</label>
                <div class="col-sm-10">
                    <select class="form-control" name="newsModuleId[]">
                        <option value="0" <%= extParams.newsModuleId == 0 ? 'selected' : '' %> class="hidden">默认模块</option>
                    <?php foreach ($newsModules as $newsModule) { ?>
                        <option value="<?php echo $newsModule['mid'] ?>" <%= extParams.newsModuleId == <?php echo $newsModule['mid'] ?> ? 'selected' : '' %>><?php echo WebUtils::u($newsModule['name']) ?></option> 
                    <?php } ?>
                    </select> 
                </div>
            </div>


        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_TOPICLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_TOPICLIST ? '' : 'hidden' %>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">选择版块: </label>
                <div class="col-sm-10">
                    <select class="form-control" name="forumId[]">
                        <option value="0" <%= extParams.forumId == 0 ? 'selected' : '' %> class="hidden">全部版块</option>
                    <?php foreach ($forumList as $fid => $title) { ?>
                        <option value="<?php echo $fid ?>" <%= extParams.forumId == <?php echo $fid; ?> ? 'selected' : '' %>><?php echo WebUtils::u($title) ?></option> 
                    <?php } ?>
                    </select> 
                </div>
            </div>
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_MESSAGELIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_MESSAGELIST ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_SURROUDING_USERLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_SURROUDING_USERLIST ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_SURROUDING_POSTLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_SURROUDING_POSTLIST ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_RECOMMEND_USERLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_RECOMMEND_USERLIST ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_SETTING+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_SETTING ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_ABOAT+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_ABOAT ? '' : 'hidden' %>">
        </div>
        <!-- wepapp 组件模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_WEBAPP+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_WEBAPP ? '' : 'hidden' %>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">wap地址：</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control input-sm" name="componentRedirect[]" value="<%= extParams.redirect %>">
                </div>
            </div>
        </div>
        <!-- fasttext/fastimage/fastcamera/fastaudio 组件模板 -->
        <div id="component-view-fastpost-<%= id %>" class="component-view-item <%= type == COMPONENT_TYPE_FASTTEXT || type == COMPONENT_TYPE_FASTIMAGE || type == COMPONENT_TYPE_FASTCAMERA || type == COMPONENT_TYPE_FASTAUDIO ? '' : 'hidden' %>">
            <div class="form-group">
                <label class="col-sm-2 control-label">发表板块：</label>
                <div class="col-sm-10">
                    <select class="input-sm" name="fastpostForumId[]">
                        <option value="0" <%= extParams.forumId == 0 ? 'selected' : '' %>>全部版块</option>
                        <?php foreach ($forumList as $fid => $title) { ?>
                            <option value="<?php echo $fid ?>" <%= extParams.forumId == <?php echo $fid; ?> ? 'selected' : '' %>><?php echo WebUtils::u($title) ?></option> 
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="isShowTopicTitle[]" <%= extParams.isShowTopicTitle ? 'checked' : '' %>> 勾选则需用户填写标题
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="isShowTopicSort[]" <%= extParams.isShowTopicSort ? 'checked' : '' %>> 勾选则显示主题分类
                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-4">
                    <button class="remove-component-btn btn btn-primary btn-sm" style="width:225px;">删　除</button>
                </div>
            </div>
        </div>
        <!-- sign 组件模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_SIGN+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_SIGN ? '' : 'hidden' %>">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-4">
                    <button class="remove-component-btn btn btn-primary btn-sm" style="width:225px;">删　除</button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">页面样式：</label>
            <div class="col-sm-10">
                <select class="form-control" name="componentStyle[]">
                    <option value="<%= COMPONENT_STYLE_FLAT %>" <%= style == COMPONENT_STYLE_FLAT ? 'selected' : '' %>>扁平样式</option>
                    <option value="<%= COMPONENT_STYLE_CARD %>" <%= style == COMPONENT_STYLE_CARD ? 'selected' : '' %>>卡片样式</option>
                    <option value="<%= COMPONENT_STYLE_IMAGE %>" <%= style == COMPONENT_STYLE_IMAGE ? 'selected' : '' %>>图片样式</option>
                </select> 
            </div>
        </div>

    </div>
    </script>
    <!-- 添加/编辑 左图右文模块 组件模板 -->
    <script type="text/template" id="news-component-edit-dlg-template">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">添加内容</h3>
            <button type="button" class="close news-component-close-btn pull-right">&times;</button>
        </div>
        <form class="form-horizontal news-component-edit-form">
        <div class="panel-body">
            <div class="component-view-container">
            </div>
        </div>
        <div class="panel-footer text-right">
            <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
            <button type="button" class="btn btn-default btn-sm news-component-close-btn">取 消</button>
        </div>
        </form>
    </div>
    </script>
    <!-- 左图右文模块 于手机ui的组件模板 -->
    <script type="text/template" id="news-component-item-template">
    <div class="edit-list">
        <div class="pull-left"><img src="<%= icon %>" style="width:50px;height:50px" class="img-rounded"></div>
        <div class="pull-left text-left page-main">
            <div class="page-title"><strong><%= title || '此处显示为标题' %></strong></div>
            <div class="page-content"><%= desc || '此处显示为文字描述' %></div>
        </div>
        <div class="text-left pull-left">
            <button class="edit-news-component-item-btn btn btn-primary btn-xs">编辑</button>
            <button class="remove-news-component-item-btn btn btn-primary btn-xs">删除</button>
        </div>
    </div>
    </script>
    <!-- 页面弹出样式用到的js -->
    <script type="text/javascript">
        $(function() {

            // 底部导航拖动
            // $(".nav-move").sortable();

            // 导航样式调整
            $('.nav-list li').hover(
                function() {
                    $(this).toggleClass('active');
                },
                function() {
                    $(this).toggleClass('active');
                }
            );

            // 添加风格区弹出框切换
            $('.close-style-pop').on({
                click:function() {
                    $('.covering').fadeToggle();
                    $('.add-style-pop').fadeToggle();
                }
            })

            // 添加组件弹出框切换
            $('.close-comp-pop').on({
                click:function() {
                    $('.covering').fadeToggle();
                    $('.add-comp-pop').fadeToggle();
                }
            })
        })
    </script>
</body>
</html>