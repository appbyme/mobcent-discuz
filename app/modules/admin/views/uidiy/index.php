<?php

/**
 * UI Diy index view
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>安米后台管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-theme-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-switch-3.2.1.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy/component-mobile.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy/module-custom.css">
    <style type="text/css">
        .mobleShow {
            width: 380px;
            background:url("<?php echo $this->rootUrl; ?>/images/admin/mobile.png") no-repeat right top;
            background-size: 380px 800px;
            text-align: center;
        }

        .nav-item-container {
            display: -moz-box;
            display: -webkit-box;
            display: box;
            float: left;
        }

        .nav-item-container .nav-item {
            -moz-box-flex: 4;
            -webkit-box-flex: 4;
            box-flex: 4;
            -webkit-box-orient: horizontal;
            -moz-box-orient: horizontal;
            -webkit-box-align: center;
            -moz-box-align: center;
            overflow: hidden;
        }
    </style>
</head>
<body>
<div class="covering"></div>
<div class="alert mobcent-alert-darker text-center" style="display:none;background:#d9534f;color:white">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <strong class="mobcent-error-info">友情提示: 为了保证数据传输的正确性，请务必使用最新版本的谷歌浏览器来进行操作。</strong>
</div>
<?php global $_G; ?>
    <!-- Static navbar -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="http://www.appbyme.com" target="_blank" style="background:url(<?php echo $this->rootUrl; ?>/images/admin/login.png);width:140px;height:50px;"></a>
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
              <a href="." class="dropdown-toggle" data-toggle="dropdown"><?php echo WebUtils::u($_G['username']); ?> <span class="caret"></span></a>
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

                        <!-- 单个组件添加/编辑弹出框 -->
                        <div id="component-edit-dlg-view" class="pic-text-pop">
                        </div>

                        <!-- 添加风格区弹出框 -->
                        <div id="custom-style-edit-dlg-view" class="add-style-pop">
                        </div>

                        <!-- 发现添加幻灯片弹出框 -->
                        <div id="discover-slider-component-edit-dlg-view" class="add-slide-pop">
                        </div>

                        <!-- 自定义添加组件弹出框 -->
                        <div id="custom-style-component-edit-dlg-view" class="add-comp-pop">
                        </div>

                        <div style="width:336px;height:550px" class="module-mobile-ui-view hidden">
                        </div>

                        <!-- 模块于手机内的编辑视图 -->
                        <div id="module-edit-mobile-view">
                        </div>

                        <button type="button" class="home-btn uidiy-sync-btn"></button>

                        <!-- 手机底部导航 -->
                        <div class="moble-bottom-nav">
                            <div class="nav-item-container">
                            </div>
                            <div class="pull-left nav-add navitem-add-btn">
                                <img src="<?php echo $this->rootUrl; ?>/images/admin/nav-add.png">
                            </div>
                        </div>

                    </div><!-- end moble-content -->

                </div>
            </div>

            <div class="col-sm-xs-8 col-sm-8 col-md-8" id="operation">

                <div id="module-edit-dlg-view" class="module-play">
                </div>

                <div class="uidiy-config-admin">
                    <div class="form-group">
                        <div class="form-group">
                            <input type="file" class="uidiy-config-file">
                        </div>
                        <div class="form-group">
                            <button class="uidiy-config-import-btn btn btn-default btn-xs">导入配置</button> 
                            <a href="<?php echo WebUtils::createUrl_oldVersion('admin/uidiy/exportconfig') ?>" class="btn btn-default btn-xs">导出配置</a> (仅导出当前已保存的配置)
                        </div>
                    </div>
                </div>

                <div class="text-left">
                    <p>
                        请点击 <button class="btn btn-primary btn-xs uidiy-save-btn"> 保存 </button> 按钮来保存你的客户端UI配置, 或者可以勾选
                        <label for="autoSaveCheckbox">
                            <input type="checkbox" id="autoSaveCheckbox" class="align-text"> 自动保存
                        </label>
                    </p>
                    <p>PS: 这里仅仅保存你在后台的配置, 如果想同步到客户端, 请点击 <a href="#save-btn" target"_self" class="save-btn">下面</a> 的同步按钮</p>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">选择导航样式</h3>
                    </div>
                    <div class="panel-body">
                        <div class="checkbox">
                            <label class="align-text"><input type="radio" checked> 底部导航</label>
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
                                <a class="module-add-btn"><img title="模块1" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png" class="img-circle"></a>
                                <div>添加模块</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="foot">
                    <p class="text-center">
                        设置完成后请务必点击 
                        <button id="save-btn" type="button" class="btn btn-primary btn-sm uidiy-sync-btn">同 步</button> 保证您所添加或设置的内容能在客户端显示！
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
        appLevel: <?php echo $appLevel; ?>,
        rootUrl: '<?php echo $this->rootUrl; ?>',
        apphash: '<?php echo MobcentDiscuz::getAppHashValue(); ?>',
        navItemIconUrlBasePath: '<?php echo $this->navItemIconBaseUrlPath; ?>',
        componentFastpostIconBaseUrlPath: '<?php echo $this->componentFastpostIconBaseUrlPath; ?>',
        componentDiscoverIconBaseUrlPath: '<?php echo $this->componentDiscoverIconBaseUrlPath; ?>',
        componentTopbarIconBaseUrlPath: '<?php echo $this->componentTopbarIconBaseUrlPath; ?>',
        moduleInitParams: <?php echo WebUtils::jsonEncode(AppbymeUIDiyModel::initModule(), 'utf-8'); ?>,
        componentInitParams: <?php echo WebUtils::jsonEncode(AppbymeUIDiyModel::initComponent(), 'utf-8'); ?>,
        layoutInitParams: <?php echo WebUtils::jsonEncode(AppbymeUIDiyModel::initLayout(), 'utf-8'); ?>,
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
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-ui-1.11.2.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-switch-3.2.1.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/admin/uidiy.js"></script>
    <!-- 底部导航模板 -->
    <script type="text/template" id="navitem-template">
    <div class="pull-left nav-column" style='background:url("<%= Appbyme.getNavIconUrl(icon) %>") no-repeat 50% 35%;background-size:70px 70px'>
        <div class="navitem-title" style="margin-top:3px;color:white"><%= title %></div>
        <div class="nav-edit hidden" style="margin-top:3px;">
            <a><span class="navitem-edit-btn">编辑</span></a>
            <% if (moduleId != MODULE_ID_DISCOVER) { %>
            <a><span class="navitem-remove-btn">删除</span></a>
            <% } %>
        </div>
    </div>
    </script>
    <!-- topbar 编辑模板 -->
    <script type="text/template" id="module-topbar-dlg-template">
    <div class="panel panel-primary">
        <form class="module-topbar-edit-form form-horizontal">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">插件设置</h3>
            <button type="button" class="close close-topbar-btn pull-right">&times;</button>
        </div>
        <input type="hidden" name="topbarIndex" id="topbarIndex" value="0">
        <div class="panel-body">
            <div class="topbar-component-view-container">
            </div>
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
                <label class="col-sm-4 control-label">导航名字: </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="navItemTitle" value="<%= title %>">
                    <p class="help-block">输入1-4个字母、数字或汉字</p>
                </div>
            </div>

            <div class="nav-icon">
                <button type="button" class="close nav-icon-close">&times;</button>
                <% for (var i = 1; i <= 49; i++) { %>
                <img class="nav-pic" data-nav-icon="<%= NAV_ITEM_ICON+i %>" src="<%= Appbyme.getNavIconUrl(NAV_ITEM_ICON+i) %>">
                <% } %>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">导航图标: </label>
                <div class="col-sm-4">
                    <button type="button" class="btn btn-primary select-nav-icon">选择图标</button>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-4 text-left">
                    <img src="<%= Appbyme.getNavIconUrl(icon) %>" style="width:60px;height:60px;background:#66ADE8" class="img-rounded nav-pic-preview">
                </div>
            </div>
            <input type="hidden" name="navItemIcon" id="navItemIcon" value="<%= icon %>">
            <div class="form-group <%= moduleId == MODULE_ID_DISCOVER ? 'hidden' : '' %>">
                <label class="col-sm-4 control-label">链接地址: </label>
                <div class="col-sm-4">
                    <select name="navItemModuleId" class="form-control">
                    <% for (var i = 0; i < Appbyme.uiModules.models.length; i++) {
                        var module = Appbyme.uiModules.models[i]; 
                    %>
                        <option value="<%= module.id %>" <%= moduleId == module.id ? 'selected' : '' %> class="<%= module.id == MODULE_ID_DISCOVER ? 'hidden' : '' %>" <%= module.id == MODULE_ID_DISCOVER ? 'disabled' : '' %>><%= module.attributes.title %></option>
                    <% } %>
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
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title pull-left"><%= id != 0 ? '编辑模块' : '添加模块' %></h3>
            <button type="button" class="close close-module-play pull-right">&times;</button>
        </div>
        <form class="module-edit-form form-horizontal">
        <div class="panel-body">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">模块名称: </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control sm" name="moduleTitle" value="<%- title %>" placeholder="">
                    <p class="help-block">请输入1-4个字母、数字或汉字作为名称</p>
                </div>
            </div>

            <% var isModuleTypeSelect = id != MODULE_ID_FASTPOST && id != MODULE_ID_DISCOVER; %>
            <div class="<%= !isModuleTypeSelect ? 'hidden' : '' %> form-group" style="position: relative;">
                <label class="col-sm-2 control-label">模块类型: </label>
                <div class="col-sm-10">
                <select id="moduleType" name="moduleType" class="form-control">
                    <option value="<%= MODULE_TYPE_FASTPOST %>" <%= type == MODULE_TYPE_FASTPOST ? 'selected' : '' %> class="<%= isModuleTypeSelect ? 'hidden' : '' %>">快速发帖</option>
                    <option value="<%= MODULE_TYPE_FULL %>" <%= type == MODULE_TYPE_FULL ? 'selected' : '' %>>单页面</option>
                    <option value="<%= MODULE_TYPE_SUBNAV %>" <%= type == MODULE_TYPE_SUBNAV ? 'selected' : '' %>>二级导航</option>
                    <option value="<%= MODULE_TYPE_NEWS %>" <%= type == MODULE_TYPE_NEWS ? 'selected' : '' %>>左图右文</option>
                    <option value="<%= MODULE_TYPE_CUSTOM %>" <%= type == MODULE_TYPE_CUSTOM ? 'selected' : '' %>>自定义页面</option>
                </select>
                </div>
                <% if (uidiyGlobalObj.appLevel == 0) { %>
                    <div style="position:absolute;left:400px;top:7px;color:red">自定义页面页面只针对付费用户开放！</div>
                <% } %> 
            </div>
            <div class="form-group module-style-select-div">
                <label for="" class="col-sm-2 control-label">模块样式: </label>
                <div class="col-sm-10">
                    <select class="form-control" name="moduleStyle">
                        <option value="<%= COMPONENT_STYLE_FLAT %>" <%= style == COMPONENT_STYLE_FLAT ? 'selected' : '' %>>扁平样式</option>
                        <option value="<%= COMPONENT_STYLE_CARD %>" <%= style == COMPONENT_STYLE_CARD ? 'selected' : '' %>>卡片样式</option>
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
    </script>
    <!-- 模块于手机内的编辑模板 -->
    <script type="text/template" id="module-edit-mobile-template">
    <img class="moble-top-show" src="<?php echo $this->rootUrl; ?>/images/admin/mobile-nav.png">
    <div class="moble-top-title">
        <img class="pull-left select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <span><%= title %></span>
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
        <img class="pull-right select-topbar-btn" src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
    </div>
    <% if (id == MODULE_ID_DISCOVER) { %>
    <div class="found-module">
        <div class="slide-img ">
            <div class="discover-slider-component-container">
            </div>
            <button type="button" class="btn btn-primary btn-xs add-discover-slider-component-item-btn" style="margin-top:5px;">点击添加更多幻灯片</button>
        </div>
        <div class="found-content">
            <div class="fixed-content">
                <div class="list-group text-left discover-default-component-container">
                </div>
            </div>
            <div class="user-content">
                <div class="list-group text-left discover-custom-component-container">
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-xs add-discover-custom-component-item-btn">点击添加更多</button>
    </div>
    <% } else if (id == MODULE_ID_FASTPOST) { %>
        <div style="height:560px;"></div>
    <% } else if (type == MODULE_TYPE_FULL) { %>
        <div style="height:560px;"></div>
    <% } else if (type == MODULE_TYPE_SUBNAV) { %>
        <div style="height:560px;"></div>
    <% } else if (type == MODULE_TYPE_NEWS) { %>
    <!-- 左图右文 -->
    <div class="pic-text list-group">
        <div class="news-component-item-container">
        </div>
        <div class="text-center" style="margin-top:10px;">
           <button type="button" class="btn btn-primary add-news-component-item-btn">点击添加更多</button>
        </div>
    </div>
    <!-- 自定义 -->
    <% } else if (type == MODULE_TYPE_CUSTOM) { %>
    <!-- 添加风格 -->
    <div class="add-style">
        <div class="custom-style-item-container">
        </div>
        <button type="button" class="btn btn-primary add-style-btn">点击添加风格区</button>
    </div>
    <% } %> 
    </script>
    <!-- 模块编辑模板 -->
    <script type="text/template" id="module-edit-detail-template">
    <% if (id == MODULE_ID_DISCOVER) { %>
    <% } else if (id == MODULE_ID_FASTPOST) { %>

    <div class="edit">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">编辑内容: </label>
        </div>
        <div class="fastpost-components-container">
        </div>
        <div class="form-group">
            <button class="more-fastpost-btn btn btn-primary btn-sm">点击添加更多发表项</button>
        <div>
        <div class="form-group fastpost-item-select-div hidden">
            <div class="col-sm-offset-2 col-sm-7">
                <label for="" class="control-label">选择发表项: </label>
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
    <div class="quick-edit" id="component-view-<%= id %>">

        <div class="form-group <%= this.uiconfig.isShow_title ? '' : 'hidden' %>">
            <label for="" class="col-sm-2 control-label">导航名称: </label>
            <div class="col-sm-10">
                <input type="text" class="form-control input-sm" name="componentTitle[]" value="<%= title %>">
            </div>
        </div>
        <div class="form-group <%= this.uiconfig.isShow_desc ? '' : 'hidden' %>">
            <label class="col-sm-2 control-label">内容简介: </label>
            <div class="col-sm-10">
                <textarea class="form-control" name="componentDesc[]" rows="3" style="resize:none;margin-bottom:8px;"><%= desc %></textarea>
            </div>
        </div>
        <div class="form-group <%= this.uiconfig.isShow_icon ? '' : 'hidden' %>">
            <label for="" class="col-sm-2 control-label">编辑图标: </label>
            <div class="col-sm-10">
                <input type="file" class="componentIconFile">
                <input type="hidden" class="componentIcon" name="componentIcon[]" value="<%= icon %>">
                <p class="help-block">请上传<span class="componentIconRatio"><%= this.uiconfig.iconRatio %></span>比例的JPG或PNG格式图片作为图标</p>
            </div>
        </div>
        <div class="form-group <%= this.uiconfig.isShow_icon ? '' : 'hidden' %>">
            <div class="col-sm-offset-2 col-sm-10 pic-preview" style="position:relative;">
                <img class="del-pic hidden" src="<?php echo $this->rootUrl; ?>/images/admin/del_pic.png">
                <img src="<%= Appbyme.getComponentIconUrl(icon) %>" style="width:50px;height:50px;" class="img-rounded component-icon-preview">
                <a class="btn btn-default btn-sm upload-component-icon-btn">点击上传图片</a>
            </div>
        </div>
        <div class="form-group <%= this.uiconfig.isShow_iconStyle ? '' : 'hidden' %>">
            <label for="" class="col-sm-2 control-label">图标样式: </label>
            <div class="col-sm-10">
                <select class="form-control componentIconStyle" name="componentIconStyle[]">
                    <option value="<%= COMPONENT_ICON_STYLE_TEXT %>" <%= iconStyle == COMPONENT_ICON_STYLE_TEXT ? 'selected' : '' %> class="<%= this.uiconfig.isShow_iconStyleText ? '' : 'hidden' %>">纯文字</option>
                    <option value="<%= COMPONENT_ICON_STYLE_IMAGE %>" <%= iconStyle == COMPONENT_ICON_STYLE_IMAGE ? 'selected' : '' %> class="<%= this.uiconfig.isShow_iconStyleImage ? '' : 'hidden' %>">单张图片</option>
                    <option value="<%= COMPONENT_ICON_STYLE_TEXT_IMAGE %>" <%= iconStyle == COMPONENT_ICON_STYLE_TEXT_IMAGE ? 'selected' : '' %> class="<%= this.uiconfig.isShow_iconStyleTextImage ? '' : 'hidden' %>">上图下文</option>
                    <option value="<%= COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN %>" <%= iconStyle == COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN ? 'selected' : '' %> class="<%= this.uiconfig.isShow_iconStyleTextOverlapDown ? '' : 'hidden' %>">文字覆盖在下</option>
                    <option value="<%= COMPONENT_ICON_STYLE_CIRCLE %>" <%= iconStyle == COMPONENT_ICON_STYLE_CIRCLE ? 'selected' : '' %> class="<%= this.uiconfig.isShow_iconStyleCircle ? '' : 'hidden' %>">圆形</option>
                    <option value="<%= COMPONENT_ICON_STYLE_NEWS %>" class="hidden" <%= iconStyle == COMPONENT_ICON_STYLE_NEWS ? 'selected' : '' %> class="<%= this.uiconfig.isShow_iconStyleNews ? '' : 'hidden' %>">左图右文</option>
                    <option value="<%= COMPONENT_ICON_STYLE_TEXT_OVERLAP_UP_VIDEO %>" <%= iconStyle == COMPONENT_ICON_STYLE_TEXT_OVERLAP_UP_VIDEO ? 'selected' : '' %> class="<%= this.uiconfig.isShow_iconStyleTextOverlapUpVideo ? '' : 'hidden' %>">视频_文字覆盖在上</option>
                    <option value="<%= COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN_VIDEO %>" <%= iconStyle == COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN_VIDEO ? 'selected' : '' %> class="<%= this.uiconfig.isShow_iconStyleTextOverlapDownVideo ? '' : 'hidden' %>">视频_文字覆盖在下</option>
                </select> 
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">链接地址: </label>
            <div class="col-sm-10">
            <select name="componentType[]" class="selectComponentType form-control" <%= this.uiconfig.isShow_typeSelect ? '' : 'disabled' %>>
                <option value="<%= COMPONENT_TYPE_EMPTY %>" <%= type == COMPONENT_TYPE_EMPTY ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeEmpty ? '' : 'hidden' %>">取消</option>
                <option value="<%= COMPONENT_TYPE_FORUMLIST %>" <%= type == COMPONENT_TYPE_FORUMLIST ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeForumlist ? '' : 'hidden' %>">版块列表</option>
                <option value="<%= COMPONENT_TYPE_NEWSLIST %>" <%= type == COMPONENT_TYPE_NEWSLIST ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeNewslist ? '' : 'hidden' %>">门户模块列表</option>
                <!-- <option value="<%= COMPONENT_TYPE_TOPICLIST %>" <%= type == COMPONENT_TYPE_TOPICLIST ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeTopiclist ? '' : 'hidden' %>">帖子列表</option> -->
                <option value="<%= COMPONENT_TYPE_TOPICLIST_SIMPLE %>" <%= type == COMPONENT_TYPE_TOPICLIST_SIMPLE ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeTopiclistSimple ? '' : 'hidden' %>">简版帖子列表</option>
                <option value="<%= COMPONENT_TYPE_POSTLIST %>" <%= type == COMPONENT_TYPE_POSTLIST ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typePostlist ? '' : 'hidden' %>">帖子详情</option>
                <option value="<%= COMPONENT_TYPE_NEWSVIEW %>" <%= type == COMPONENT_TYPE_NEWSVIEW ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeNewsview ? '' : 'hidden' %>">文章详情</option>
                <option value="<%= COMPONENT_TYPE_MODULEREF %>" <%= type == COMPONENT_TYPE_MODULEREF ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeModuleRef ? '' : 'hidden' %>">模块指向</option>
                <option value="<%= COMPONENT_TYPE_WEBAPP %>" <%= type == COMPONENT_TYPE_WEBAPP ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeWebapp ? '' : 'hidden' %>">外部wap页</option>
                <option value="<%= COMPONENT_TYPE_USERINFO %>" <%= type == COMPONENT_TYPE_USERINFO ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeUserinfo ? '' : 'hidden' %>">用户中心</option>
                <option value="<%= COMPONENT_TYPE_USERLIST %>" <%= type == COMPONENT_TYPE_USERLIST ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeUserlist ? '' : 'hidden' %>">用户列表</option>
                <option value="<%= COMPONENT_TYPE_MESSAGELIST %>" <%= type == COMPONENT_TYPE_MESSAGELIST ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeMessagelist ? '' : 'hidden' %>">消息列表</option>
                <option value="<%= COMPONENT_TYPE_SETTING %>" <%= type == COMPONENT_TYPE_SETTING ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeSetting ? '' : 'hidden' %>">设置</option>
                <option value="<%= COMPONENT_TYPE_ABOUT %>" <%= type == COMPONENT_TYPE_ABOUT ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeAbout ? '' : 'hidden' %>">关于</option>
                <option value="<%= COMPONENT_TYPE_WEATHER %>" <%= type == COMPONENT_TYPE_WEATHER ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeWeather ? '' : 'hidden' %>">天气</option>
                <option value="<%= COMPONENT_TYPE_SEARCH %>" <%= type == COMPONENT_TYPE_SEARCH ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeSearch ? '' : 'hidden' %>">搜索</option>
                <option value="<%= COMPONENT_TYPE_FASTTEXT %>" <%= type == COMPONENT_TYPE_FASTTEXT ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeFasttext ? '' : 'hidden' %>">发表文字</option>
                <option value="<%= COMPONENT_TYPE_FASTIMAGE %>" <%= type == COMPONENT_TYPE_FASTIMAGE ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeFastimage ? '' : 'hidden' %>">发表图片</option>
                <option value="<%= COMPONENT_TYPE_FASTCAMERA %>" <%= type == COMPONENT_TYPE_FASTCAMERA ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeFastcamera ? '' : 'hidden' %>">拍照发表</option>
                <option value="<%= COMPONENT_TYPE_FASTAUDIO %>" <%= type == COMPONENT_TYPE_FASTAUDIO ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeFastaudio ? '' : 'hidden' %>">发表语音</option>
                <option value="<%= COMPONENT_TYPE_SIGN %>" <%= type == COMPONENT_TYPE_SIGN ? 'selected' : '' %> class="<%= this.uiconfig.isShow_typeSign ? '' : 'hidden' %>">签到</option>
            </select>
            </div>
        </div>

        <div id="component-view-<% print(COMPONENT_TYPE_EMPTY+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_EMPTY ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_FORUMLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_FORUMLIST ? '' : 'hidden' %>">
        <!--
            <div class="form-group">
                <label class="col-sm-2 control-label">设置样式: </label>
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
        --> 
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_NEWSLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_NEWSLIST ? '' : 'hidden' %>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">选择模块: </label>
                <div class="col-sm-10">
                    <select class="form-control" name="newsModuleId[]">
                        <option value="0" <%= extParams.newsModuleId == 0 ? 'selected' : '' %> class="hidden">请选择模块</option>
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
                    <select class="form-control" name="topicForumId[]">
                        <option value="0" <%= extParams.forumId == 0 ? 'selected' : '' %> class="">全部版块</option>
                    <?php foreach ($forumList as $fid => $title) { ?>
                        <option value="<?php echo $fid ?>" <%= extParams.forumId == <?php echo $fid; ?> ? 'selected' : '' %>><?php echo WebUtils::u($title) ?></option> 
                    <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_TOPICLIST_SIMPLE+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_TOPICLIST_SIMPLE ? '' : 'hidden' %>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">选择版块: </label>
                <div class="col-sm-10">
                    <select class="form-control" name="topicSimpleForumId[]">
                        <option value="0" <%= extParams.forumId == 0 ? 'selected' : '' %> class="">全部版块</option>
                    <?php foreach ($forumList as $fid => $title) { ?>
                        <option value="<?php echo $fid ?>" <%= extParams.forumId == <?php echo $fid; ?> ? 'selected' : '' %>><?php echo WebUtils::u($title) ?></option> 
                    <?php } ?>
                    </select> 
                </div>
            </div>
        </div>
        <!-- 帖子详情模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_POSTLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_POSTLIST ? '' : 'hidden' %>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">主题id: </label>
                <div class="col-sm-10">
                    <input type="number" class="form-control input-sm" name="topicId[]" value="<%= extParams.topicId %>">
                </div>
            </div>
        </div>
        <!-- 文章详情模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_NEWSVIEW+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_NEWSVIEW ? '' : 'hidden' %>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">文章id: </label>
                <div class="col-sm-10">
                    <input type="number" class="form-control input-sm" name="articleId[]" value="<%= extParams.articleId %>">
                </div>
            </div>
        </div>
        <!-- 用户中心模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_USERINFO+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_USERINFO ? '' : 'hidden' %>">

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <lable><input type="checkbox" name="isShowMessagelist[]" <%= extParams.isShowMessagelist ? 'checked' : '' %>>     是否显示我的消息</lable>
                </div>
            </div>
        </div>
        <!-- 用户列表 组件模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_USERLIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_USERLIST ? '' : 'hidden' %>">
            <div class="form-group">
                <label class="col-sm-2 control-label">条件选择:</label>
                <div class="col-sm-10">
                    <select name="userlistFilter[]" class="form-control">
                        <option value="<%= USERLIST_FILTER_ALL %>" <%= extParams.filter == USERLIST_FILTER_ALL ? 'selected' : '' %>>全部</option> 
                        <option value="<%= USERLIST_FILTER_FRIEND %>" <%= extParams.filter == USERLIST_FILTER_FRIEND ? 'selected' : '' %>>好友</option> 
                        <option value="<%= USERLIST_FILTER_FOLLOW %>" <%= extParams.filter == USERLIST_FILTER_FOLLOW ? 'selected' : '' %>>关注</option> 
                        <option value="<%= USERLIST_FILTER_FOLLOWED %>" <%= extParams.filter == USERLIST_FILTER_FOLLOWED ? 'selected' : '' %>>粉丝</option> 
                        <option value="<%= USERLIST_FILTER_RECOMMEND %>" <%= extParams.filter == USERLIST_FILTER_RECOMMEND ? 'selected' : '' %>>推荐</option> 
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">排序方式:</label>
                <div class="col-sm-10">
                    <select name="userlistOrderby[]" class="form-control">
                        <option value="<%= USERLIST_ORDERBY_DATELINE %>" <%= extParams.orderby == USERLIST_ORDERBY_DATELINE ? 'selected' : '' %>>按默认排序</option> 
                        <option value="<%= USERLIST_ORDERBY_REGISTER %>" <%= extParams.orderby == USERLIST_ORDERBY_REGISTER ? 'selected' : '' %>>按注册时间倒序排序</option> 
                        <option value="<%= USERLIST_ORDERBY_LOGIN %>" <%= extParams.orderby == USERLIST_ORDERBY_LOGIN ? 'selected' : '' %>>按登陆时间倒序排序</option> 
                        <option value="<%= USERLIST_ORDERBY_FOLLOWED %>" <%= extParams.orderby == USERLIST_ORDERBY_FOLLOWED ? 'selected' : '' %>>按粉丝最多倒序排序</option> 
                        <option value="<%= USERLIST_ORDERBY_DISTANCE %>" <%= extParams.orderby == USERLIST_ORDERBY_DISTANCE ? 'selected' : '' %>>按距离倒序排序</option> 
                    </select>
                </div>
            </div>
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_MESSAGELIST+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_MESSAGELIST ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_SETTING+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_SETTING ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_ABOUT+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_ABOUT ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_WEATHER+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_WEATHER ? '' : 'hidden' %>">
        </div>
        <div id="component-view-<% print(COMPONENT_TYPE_SEARCH+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_SEARCH ? '' : 'hidden' %>">
        </div>
        <!-- wepapp 组件模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_WEBAPP+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_WEBAPP ? '' : 'hidden' %>">
            <% if (uidiyGlobalObj.appLevel == 0) { %>
                <div style="color:red;" class="mobcent-pay-info">外部wap页只针对付费用户开放！</div>
            <% } %> 
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">wap地址: </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control input-sm" name="componentRedirect[]" value="<%= extParams.redirect %>" placeholder="http://xxx">
                </div>
            </div>
        </div>
        <!-- 模块指向 组件模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_MODULEREF+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_MODULEREF ? '' : 'hidden' %>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">选择模块: </label>
                <div class="col-sm-10">
                    <select class="form-control" name="moduleId[]">
                    <% for (var i = 0; i < Appbyme.uiModules.length; i++) { %>
                        <% var module = Appbyme.uiModules.models[i].attributes; %>
                        <% if (module.type != MODULE_TYPE_FASTPOST) { %>
                        <option value="<%= module.id %>" <%= extParams.moduleId == module.id ? 'selected' : '' %>><%= module.title %></option> 
                        <% } %>
                    <% } %>
                    </select>
                </div>
            </div>
        </div>
        <!-- fasttext/fastimage/fastcamera/fastaudio 组件模板 -->
        <div id="component-view-fastpost-<%= id %>" class="component-view-item <%= type == COMPONENT_TYPE_FASTTEXT || type == COMPONENT_TYPE_FASTIMAGE || type == COMPONENT_TYPE_FASTCAMERA || type == COMPONENT_TYPE_FASTAUDIO ? '' : 'hidden' %>">
            <div class="form-group">
                <label class="col-sm-2 control-label">发表板块: </label>
                <div class="col-sm-10">
                    <select class="input-sm form-control" name="fastpostForumIds[]" multiple>
                        <?php foreach ($forumList as $fid => $title) { ?>
                            <option value="<?php echo $fid ?>" <%= extParams.fastpostForumIds.indexOf(<?php echo $fid; ?>) != -1 ? 'selected' : '' %>><?php echo WebUtils::u($title) ?></option> 
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <label class="checkbox-inline">
                        <input type="checkbox" name="isShowTopicTitle[]" <%= extParams.isShowTopicTitle ? 'checked' : '' %>> 勾选则需用户填写标题
                    </label>
                    <!-- 
                    <label class="checkbox-inline">
                        <input type="checkbox" name="isShowTopicSort[]" <%= extParams.isShowTopicSort ? 'checked' : '' %>> 勾选则显示主题分类
                    </label>
                    -->
                </div>
            </div>
        </div>
        <!-- sign 组件模板 -->
        <div id="component-view-<% print(COMPONENT_TYPE_SIGN+'-'+id) %>" class="component-view-item <%= type == COMPONENT_TYPE_SIGN ? '' : 'hidden' %>">
        </div>
        <div class="form-group component-style-select-div <%= this.uiconfig.isShow_style ? '' : 'hidden' %>">
            <label for="" class="col-sm-2 control-label">页面样式: </label>
            <div class="col-sm-10">
                <select class="form-control" name="componentStyle[]">
                    <option value="<%= COMPONENT_STYLE_FLAT %>" <%= style == COMPONENT_STYLE_FLAT ? 'selected' : '' %>>扁平样式</option>
                    <option value="<%= COMPONENT_STYLE_CARD %>" <%= style == COMPONENT_STYLE_CARD ? 'selected' : '' %>>卡片样式</option>
                    <option value="<%= COMPONENT_STYLE_IMAGE %>" <%= style == COMPONENT_STYLE_IMAGE ? 'selected' : '' %>>图片样式</option>
                    <option value="<%= COMPONENT_STYLE_IMAGE_BIG %>" <%= style == COMPONENT_STYLE_IMAGE_BIG ? 'selected' : '' %>>大图样式</option>
                    <option value="<%= COMPONENT_STYLE_IMAGE_SUDOKU %>" <%= style == COMPONENT_STYLE_IMAGE_SUDOKU ? 'selected' : '' %>>类朋友圈样式</option>
                    <option value="<%= COMPONENT_STYLE_1 %>" <%= style == COMPONENT_STYLE_1 ? 'selected' : '' %>>样式1</option>
                    <option value="<%= COMPONENT_STYLE_2 %>" <%= style == COMPONENT_STYLE_2 ? 'selected' : '' %>>样式2</option>
                </select> 
            </div>
        </div>
        <div class="form-group <%= this.uiconfig.isShow_delete ? '' : 'hidden' %>">
            <div class="col-sm-offset-2 col-sm-4">
                <button class="remove-component-btn btn btn-primary btn-sm" style="width:200px;">删　除</button>
            </div>
        </div>
    </div>
    </script>
    <!-- 单个组件编辑框 模板 -->
    <script type="text/template" id="component-edit-dlg-template">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">添加内容</h3>
            <button type="button" class="close component-close-btn pull-right">&times;</button>
        </div>
        <form class="form-horizontal component-edit-form">
        <div class="panel-body">
            <div class="component-view-container">
            </div>
        </div>
        <div class="panel-footer text-right">
            <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
            <button type="button" class="btn btn-default btn-sm component-close-btn">取 消</button>
        </div>
        </form>
    </div>
    </script>
    <!-- 左图右文模块 于手机ui的组件模板 -->
    <script type="text/template" id="news-component-item-template">
        <div class="pull-left"><img src="<%= icon %>" style="width:50px;height:50px" class="img-rounded"></div>
        <div class="pull-left text-left page-main">
            <div class="page-title"><strong><%= title || '此处显示为标题' %></strong></div>
            <div class="page-content"><%= desc || '此处显示为文字描述' %></div>
        </div>
        <div class="text-left pull-left page-btn">
            <button class="edit-news-component-item-btn btn btn-primary btn-xs">编辑</button>
            <button class="remove-news-component-item-btn btn btn-primary btn-xs">删除</button>
        </div>
    </script>
    <!-- 发现幻灯片组件模板 -->
    <script type="text/template" id="discover-slider-component-template">
    <% if (componentList.length > 0)  { %>
    <div class="carousel slide carousel-example-generic_one" data-ride="carousel" data-interval="3000" style="width:337px;height:150px;">
        <!-- 圆点 -->
        <ol class="carousel-indicators">
        <% for (var i = 0; i < componentList.length; i++) { %>
            <li data-target=".carousel-example-generic_one" data-slide-to="<%= i %>" class="<%= i == 0 ? 'active' : '' %>"></li>
        <% } %>
        </ol>
        <!-- 图片区域，item是一个图片 -->
        <div class="carousel-inner">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
            <div class="item <%= i == 0 ? 'active' : '' %>">
                <img src="<%= component.icon %>" alt="" style="width:337px;height:150px;">
                <div class="carousel-caption">
                    <p><%= component.title %></p> 
                </div>
            </div>
            <% } %>
        </div>
        <% if (componentList.length > 1) { %>
        <a class="left carousel-control" href=".carousel-example-generic_one" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="right carousel-control" href=".carousel-example-generic_one" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
        <% } %>
    </div>
    <% } %>
    </script>
    <!-- 发现幻灯片 添加/编辑 对话框 模板 -->
    <script type="text/template" id="discover-slider-component-edit-dlg-template">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">添加幻灯片</h3>
            <button type="button" class="close pull-right component-close-btn">&times;</button>
        </div>
        <form class="form-horizontal component-edit-form">
        <div class="panel-body">
            <div class="form-group">
                <div class="col-sm-10">
                <input type="hidden" name="layoutStyleSelect" value="<%= COMPONENT_STYLE_DISCOVER_SLIDER %>">
                </div>
            </div>

            <div class="component-view-container">
            </div>

            <div class="form-group">
                <button class="add-component-item-btn btn btn-info btn-sm" style="width:280px;">添加组件</button>
            </div>
        </div>
        <div class="panel-footer text-right">
            <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
            <button type="button" class="btn btn-default btn-sm component-close-btn">取 消</button>
        </div>
        </form>
    </div>
    </script>
    <!-- 发现固定项在手机ui的组件模板 -->
    <script type="text/template" id="discover-default-component-item-template">
        <img class="img-rounded pull-left" src="<%= Appbyme.getComponentIconUrl(icon) %>">
        <div class="pull-left discover-title"><%= title %></div>
        <div class="pull-left oper-btn text-right">
            <input type="checkbox" data-on-text="显示" data-off-text="隐藏" data-size="mini" class="discover-item-switch" <%= extParams.isHidden ? '' : 'checked' %> />
        </div>
    </script>
    <!-- 发现用户项在手机ui的组件模板 -->
    <script type="text/template" id="discover-custom-component-item-template">
      <img class="img-rounded pull-left" src="<%= icon %>">
      <div class="pull-left discover-title"><%= title %></div>
      <div class="pull-left oper-btn text-right">
          <button type="button" class="btn btn-primary btn-xs edit-discover-item-btn">编辑</button>
          <button type="button" class="btn btn-primary btn-xs remove-discover-item-btn">删除</button>
      </div>
    </script>
    <!-- 添加/编辑 自定义模块 风格组件模板 -->
    <script type="text/template" id="custom-style-edit-dlg-template">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">添加风格区</h3>
            <button type="button" class="close style-close-btn pull-right">&times;</button>
        </div>
        <form class="form-horizontal style-edit-form">
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-4 control-label">选择风格区: </label>
                <div class="col-sm-4">
                    <select class="form-control input-sm" name="layoutStyle">
                        <option value="<%= COMPONENT_STYLE_LAYOUT_DEFAULT %>" <%= style == COMPONENT_STYLE_LAYOUT_DEFAULT ? 'selected' : '' %>>默认风格</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_IMAGE %>" <%= style == COMPONENT_STYLE_LAYOUT_IMAGE ? 'selected' : '' %>>图片墙风格</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_LINE %>" <%= style == COMPONENT_STYLE_LAYOUT_LINE ? 'selected' : '' %>>线分割风格</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">是否显示标题: </label>
                <div class="col-sm-8 text-left">
                    <label class="radio-inline">
                    <input class="isShowStyleHeaderRadio" type="radio" name="isShowStyleHeader" <%= extParams.styleHeader.isShow ? 'checked' : '' %> value="1">是</label>
                    <label class="radio-inline">
                    <input class="isShowStyleHeaderRadio" type="radio" name="isShowStyleHeader" <%= !extParams.styleHeader.isShow ? 'checked' : '' %> value="0">否</label>
                </div>
            </div>

            <div class="style-header-container">

            <div class="form-group">
                <label class="col-sm-4 control-label">风格区标题: </label>
                <div class="col-sm-8">
                    <input type="text" class="form-control input-sm" name="styleHeaderTitle" value="<%= extParams.styleHeader.title %>">
                    <p class="help-block">输入1-9个汉字、数字或字母</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">标题位置: </label>
                <div class="col-sm-8" style="padding:0px 0px 0px 15px;">
                    <label class="radio-inline pull-left">
                    <input type="radio" name="styleHeaderPosition" <%= extParams.styleHeader.position ? 'checked' : '' %> value="1">风格区顶部</label>
                    <label class="radio-inline pull-left">
                    <input type="radio" name="styleHeaderPosition" <%= !extParams.styleHeader.position ? 'checked' : '' %> value="0">风格区底部</label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">是否显示更多: </label>
                <div class="col-sm-8" style="padding:0px 0px 0px 15px;">
                    <label class="radio-inline pull-left">
                    <input class="isShowStyleHeaderMoreRadio" type="radio" value="1" name="isShowStyleHeaderMore" <%= extParams.styleHeader.isShowMore ? 'checked' : '' %>>是</label>
                    <label class="radio-inline pull-left">
                    <input class="isShowStyleHeaderMoreRadio" type="radio" value="0" name="isShowStyleHeaderMore" <%= !extParams.styleHeader.isShowMore ? 'checked' : '' %>>否</label>
                </div>
            </div>

            <div class="component-view-container">
            </div>

            </div>
        </div>
        <div class="panel-footer text-right">
            <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
            <button type="button" class="btn btn-default btn-sm style-close-btn">取 消</button>
        </div>
        </form>
    </div>              
    </script>
    <!-- 添加/编辑 自定义模块 风格内组件模板 -->
    <script type="text/template" id="custom-style-component-edit-dlg-template">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title pull-left">添加组件</h3>
            <button type="button" class="close pull-right style-component-close-btn">&times;</button>
        </div>
        <form class="form-horizontal style-component-edit-form">
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-2 control-label">选择视窗类型: </label>
                <div class="col-sm-10">
                    <select class="form-control input-sm layoutStyleSelect" name="layoutStyle">
                        <option value="<%= COMPONENT_STYLE_LAYOUT_ONE_COL_HIGH %>" <%= style == COMPONENT_STYLE_LAYOUT_ONE_COL_HIGH ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutOneColHigh ? '' : 'hidden' %>">单栏样式(高)</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_ONE_COL_LOW %>" <%= style == COMPONENT_STYLE_LAYOUT_ONE_COL_LOW ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutOneColLow ? '' : 'hidden' %>">单栏样式(低)</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_TWO_COL_TEXT %>" <%= style == COMPONENT_STYLE_LAYOUT_TWO_COL_TEXT ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutTwoColText ? '' : 'hidden' %>">双栏文字</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_TWO_COL_HIGH %>" <%= style == COMPONENT_STYLE_LAYOUT_TWO_COL_HIGH ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutTwoColHigh ? '' : 'hidden' %>">双栏样式(高)</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_TWO_COL_MID %>" <%= style == COMPONENT_STYLE_LAYOUT_TWO_COL_MID ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutTwoColMid ? '' : 'hidden' %>">双栏样式(中)</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_TWO_COL_LOW %>" <%= style == COMPONENT_STYLE_LAYOUT_TWO_COL_LOW ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutTwoColLow ? '' : 'hidden' %>">双栏样式(低)</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_THREE_COL_TEXT %>" <%= style == COMPONENT_STYLE_LAYOUT_THREE_COL_TEXT ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutThreeColText ? '' : 'hidden' %>">三栏文字</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_THREE_COL_HIGH %>" <%= style == COMPONENT_STYLE_LAYOUT_THREE_COL_HIGH ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutThreeColHigh ? '' : 'hidden' %>">三栏样式(高)</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_THREE_COL_MID %>" <%= style == COMPONENT_STYLE_LAYOUT_THREE_COL_MID ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutThreeColMid ? '' : 'hidden' %>">三栏样式(中)</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_THREE_COL_LOW %>" <%= style == COMPONENT_STYLE_LAYOUT_THREE_COL_LOW ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutThreeColLow ? '' : 'hidden' %>">三栏样式(低)</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_FOUR_COL %>" <%= style == COMPONENT_STYLE_LAYOUT_FOUR_COL ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutFourCol ? '' : 'hidden' %>">四栏样式</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_ONE_COL_ONE_ROW %>" <%= style == COMPONENT_STYLE_LAYOUT_ONE_COL_ONE_ROW ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutOneColOneRow ? '' : 'hidden' %>">1(大)+1样式</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_ONE_COL_TWO_ROW %>" <%= style == COMPONENT_STYLE_LAYOUT_ONE_COL_TWO_ROW ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutOneColTwoRow ? '' : 'hidden' %>">1+2样式</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_ONE_COL_THREE_ROW %>" <%= style == COMPONENT_STYLE_LAYOUT_ONE_COL_THREE_ROW ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutOneColThreeRow ? '' : 'hidden' %>">1+3样式</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_ONE_ROW_ONE_COL %>" <%= style == COMPONENT_STYLE_LAYOUT_ONE_ROW_ONE_COL ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutOneRowOneCol ? '' : 'hidden' %>">1+1(大)样式</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_TWO_ROW_ONE_COL %>" <%= style == COMPONENT_STYLE_LAYOUT_TWO_ROW_ONE_COL ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutTwoRowOneCol ? '' : 'hidden' %>">2+1样式</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_THREE_ROW_ONE_COL %>" <%= style == COMPONENT_STYLE_LAYOUT_THREE_ROW_ONE_COL ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutThreeRowOneCol ? '' : 'hidden' %>">3+1样式</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_SLIDER %>" <%= style == COMPONENT_STYLE_LAYOUT_SLIDER ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutSlider ? '' : 'hidden' %>">幻灯片样式</option>
                        <option value="<%= COMPONENT_STYLE_LAYOUT_NEWS_AUTO %>" <%= style == COMPONENT_STYLE_LAYOUT_NEWS_AUTO ? 'selected' : '' %> class="<%= this.uiconfig.isShow_layoutNewsAuto ? '' : 'hidden' %>">列表自动样式</option>
                        <!--
                        <option value="<%= COMPONENT_STYLE_LAYOUT_NEWS_MANUAL %>" <%= style == COMPONENT_STYLE_LAYOUT_NEWS_MANUAL ? 'selected' : '' %>>列表手动样式</option>
                        -->
                    </select>
                </div>
            </div>

            <div class="component-view-container">
            </div>
            <div class="form-group">
                <button class="add-component-item-btn btn btn-info btn-sm" style="width:280px;">添加组件</button>
            </div>
        </div>
        <div class="panel-footer text-right">
            <input type="submit" class="btn btn-primary btn-sm" value="确定" >  
            <button type="button" class="btn btn-default btn-sm style-component-close-btn">取 消</button>
        </div>
        </form>
    </div>
    </script>
    <!-- 自定义模块 于手机ui的风格模板 -->
    <script type="text/template" id="custom-style-item-template">
    <div class="single-style">
        <div class="style-content">
            <div class="style-title <%= extParams.styleHeader.isShow && extParams.styleHeader.position ? '' : 'hidden' %>">
                <p class="pull-left"><%= extParams.styleHeader.title ? extParams.styleHeader.title : '此处是风格区标题' %></p>
                <span class="pull-right" <%= extParams.styleHeader.isShowMore ? '' : 'hidden' %>>更多</span>
            </div>
            <div class="custom-style-component-item-container">
            </div>
            <button type="button" class="btn btn-primary btn-xs add-style-component-btn">点击添加组件</button>
            <div class="style-title <%= extParams.styleHeader.isShow && !extParams.styleHeader.position ? '' : 'hidden' %>">
                <p class="pull-left"><%= extParams.styleHeader.title ? extParams.styleHeader.title : '此处是风格区标题' %></p>
                <span class="pull-right" <%= extParams.styleHeader.isShowMore ? '' : 'hidden' %>>更多</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-xs edit-custom-style-item-btn">编辑该风格区</button>
        <button type="button" class="btn btn-primary btn-xs remove-custom-style-item-btn">删除该风格区</button>
    </div>
    </script>
    <!-- 自定义模块 于手机ui的组件模板 -->
    <script type="text/template" id="custom-style-component-item-template">
    <% if (style == COMPONENT_STYLE_LAYOUT_ONE_COL_HIGH) { %>
        <div class="layout-one-col-high">
        <% for (var i = 0; i < componentList.length; i++) { %>
        <% var component = componentList[i].attributes; %>
            <div class="textOverlapDown">
                <img src="<%= component['icon'] %>" style="width:320px;height:320px;" class="img-rounded">
                <div class="textOverlapDown-title">
                    <div class="textoverlapdown-color"><%= component['title'] %></div>
                </div>
            </div>
        <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_ONE_COL_LOW) { %>
        <div class="custom-style-layout-one-col-low">
        <% for (var i = 0; i < componentList.length; i++) { %>
        <% var component = componentList[i].attributes; %>
            <div class="textOverlapDown">
                <img src="<%= component['icon'] %>" style="width:320px;height:160px;" class="img-rounded">
                <div class="textOverlapDown-title">
                    <div class="textoverlapdown-color"><%= component['title'] %></div>
                </div>
            </div>
        <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_TWO_COL_TEXT) { %>
        <div class="custom-layouttwocoltext">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="layouttwocoltext-title"><%= component['title'] %></div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_TWO_COL_HIGH) { %>
        <div class="layouttwocol-high">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="layouttwocol-hight-img">
                    <img src="<%= component['icon'] %>" class="img-rounded" style="width:155px;height:155px;">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_TWO_COL_MID) { %>
        <div class="layouttwocol-mid">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-two-col-mid">
                    <img src="<%= component['icon'] %>" class="img-rounded" style="width:155px;height:130px;">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_TWO_COL_LOW) { %>
        <div class="layout-two-col-low">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-two-col-low">
                    <img src="<%= component['icon'] %>" class="img-rounded" style="width:155px;height:70px;">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_THREE_COL_TEXT) { %>
        <div class="custom-layoutThreeColText">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="layoutthreecoltext-title"><%= component['title'] %></div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_THREE_COL_HIGH) { %>
        <div class="layout-three-col-high">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-three-col-high">
                    <img src="<%= component['icon'] %>" class="img-rounded" style="width:100px;height:130px;">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_THREE_COL_MID) { %>
        <div class="layout-three-col-mid">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-three-col-mid">
                    <img src="<%= component['icon'] %>" class="img-rounded" style="width:100px;height:100px;">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_THREE_COL_LOW) { %>
        <div class="layout-three-col-low">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-three-col-low">
                    <img src="<%= component['icon'] %>" class="img-rounded" style="width:100px;height:70px;">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_FOUR_COL) { %>
        <div class="layout-four-col">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-four-col">
                    <img src="<%= component['icon'] %>" class="img-rounded" style="width:75px;height:75px;">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_ONE_COL_ONE_ROW) { %>
        <div class="layout-one-col-one-row">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-one-col-one-row-<%= i == 0 ? 'left' : 'right' %>">
                    <img src="<%= component['icon'] %>" class="img-rounded layout-one-col-one-row-<%= i == 0 ? 'left' : 'right' %>">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_ONE_COL_TWO_ROW) { %>
        <div class="layout-one-col-two-row">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-one-col-two-row-<%= i == 0 ? 'left' : 'right' %>">
                    <img src="<%= component['icon'] %>" class="img-rounded layout-one-col-two-row-<%= i == 0 ? 'left' : 'right' %>">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_ONE_COL_THREE_ROW) { %>
        <div class="layout-one-col-three-row">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-one-col-three-row-<%= i == 0 ? 'left' : 'right' %>">
                    <img src="<%= component['icon'] %>" class="img-rounded layout-one-col-three-row-<%= i == 0 ? 'left' : 'right' %>">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_ONE_ROW_ONE_COL) { %>
        <div class="layout-one-row-one-col">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-one-row-one-col-<%= i == 0 ? 'left' : 'right' %>">
                    <img src="<%= component['icon'] %>" class="img-rounded layout-one-row-one-col-<%= i == 0 ? 'left' : 'right' %>">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_TWO_ROW_ONE_COL) { %>
        <div class="layout-two-row-one-col">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-two-row-one-col-<%= i != 2 ? 'left' : 'right' %>">
                    <img src="<%= component['icon'] %>" class="img-rounded layout-two-row-one-col-<%= i != 2 ? 'left' : 'right' %>">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_THREE_ROW_ONE_COL) { %>
        <div class="layout-three-row-one-col">
            <% for (var i = 0; i < componentList.length; i++) { %>
            <% var component = componentList[i].attributes; %>
                <div class="custom-style-layout-three-row-one-col-<%= i != 3 ? 'left' : 'right' %>">
                    <img src="<%= component['icon'] %>" class="img-rounded layout-three-row-one-col-<%= i != 3 ? 'left' : 'right' %>">
                    <div class="custom-covering-title">
                        <div class="textoverlapdown-color"><%= component['title'] %></div>
                    </div>
                </div>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_SLIDER) { %>
        <div class="carousel slide carousel-example-generic_one" data-ride="carousel" data-interval="3000" style="width:337px;height:150px;">
            <!-- 圆点 -->
            <ol class="carousel-indicators">
            <% for (var i = 0; i < componentList.length; i++) { %>
                <li data-target=".carousel-example-generic_one" data-slide-to="<%= i %>" class="<%= i == 0 ? 'active' : '' %>"></li>
            <% } %>
            </ol>
            <!-- 图片区域，item是一个图片 -->
            <div class="carousel-inner">
                <% for (var i = 0; i < componentList.length; i++) { %>
                <% var component = componentList[i].attributes; %>
                <div class="item <%= i == 0 ? 'active' : '' %>">
                    <img src="<%= component.icon %>" alt="" style="width:337px;height:150px;">
                    <div class="carousel-caption">
                        <p><%= component.title %></p> 
                    </div>
                </div>
                <% } %>
            </div>
            <% if (componentList.length > 1) { %>
            <a class="left carousel-control" href=".carousel-example-generic_one" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a class="right carousel-control" href=".carousel-example-generic_one" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            <% } %>
        </div>
    <% } else if (style == COMPONENT_STYLE_LAYOUT_NEWS_AUTO) { %>
        <div class="newsauto-component-item-container">
        </div>
    <% } %>
    <div class="text-left" style="margin-bottom:5px;">
        <button class="edit-style-component-item-btn btn btn-primary btn-xs">编辑</button>
        <button class="remove-style-component-item-btn btn btn-primary btn-xs">删除</button>
    </div>
    </script>
    <!-- 页面弹出样式用到的js -->
    <script type="text/javascript">
        $(function() {
            // 导航样式调整
            $('.nav-list li').hover(
                function() {
                    $(this).toggleClass('active');
                },
                function() {
                    $(this).toggleClass('active');
                }
            )
            
            $('.save-btn').on({
                click:function() {
                  $('#foot').effect('bounce', '2000', callback);
                }
            })

            function callback() {
                $('#save-btn').effect('pulsate', '2000');
            }

            var browserInfo = "<?php echo $browserInfo; ?>";
            if (browserInfo < '37.0.2062.124') {
                $('.mobcent-alert-darker a').remove();
                $('.mobcent-alert-darker').toggle("drop");
                $('#uidiy-main-view').remove();
                // setTimeout("$('.alert-darker').fk(8000);", 1000);
            }
        })

        // 根据付费信息的提示语
        function showTipsByAppLevel() {
            // if (appLevel == 0) {
            //     $('.mobcent-error-info').html('自定义页面和接入外部wap页面的功能只针对付费用户开放！');
            //     $('.mobcent-alert-darker').toggle("drop");
            //     $("body,html").scrollTop(0);
            //     setTimeout("$('.mobcent-alert-darker').fadeOut(2000);", 1000);
            // }
        }
    </script>
</body>
</html>