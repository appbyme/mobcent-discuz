<?php

/**
 * 微生活
 *
 * @author HanPengyu
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>微生活管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-theme-3.2.0.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/bootstrap-switch-3.2.1.min.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-wshdiy.css">
    <link rel="stylesheet" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy/component-mobile.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->rootUrl; ?>/css/appbyme-admin-uidiy/module-custom.css">
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-3.2.0.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/jquery-ui-1.11.2.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/bootstrap-switch-3.2.1.min.js"></script>
    <script src="<?php echo $this->rootUrl; ?>/js/admin/wshdiy.js"></script>
    <style type="text/css">
        .wshdiy-mobile {
            width: 350px;
            height: 700px;
            background:url("<?php echo $this->rootUrl; ?>/images/admin/mobile.png") no-repeat right top;
            background-size: 350px 700px;
            text-align: center;
            /*border: 1px solid green;*/
            position: relative;
        }
    </style>
</head>
<?php global $_G; ?>
<body>
<div class="covering"></div>
<!-- <div class="error-prompt"></div> -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="http://www.appbyme.com" target="_blank" style="background:url(<?php echo $this->rootUrl; ?>/images/admin/login.png);width:140px;height:50px;"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav nav-list">
            <li class=""><a href="<?php echo $this->dzRootUrl; ?>" target="_blank">网站首页</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">应用管理 <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/uidiy">自定义管理</a></li>
                    <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/wshdiy">微生活管理</a></li>
                </ul>
            </li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/admin.php" target="_blank">Discuz!管理中心</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/plugin.php?id=appbyme_app:download" target="_blank">应用下载</a></li>
            <li><a href="<?php echo $this->dzRootUrl; ?>/mobcent/requirements/index.php" target="_blank">配置需求</a></li>
            <li><a href="http://bbs.appbyme.com/forum-57-2.html" target="_blank">帮助文档</a></li>
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

    <div class="wshdiy-main">

        <div class="wshdiy-left">
            <div class="wshdiy-mobile">
                <iframe class="wap-preview" src="http://wsh.appbyme.com/"></iframe>
            </div>
        </div>

        <div class="wshdiy-right">

            <!-- 添加公共服务页 -->
            <div class="add-public">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left public-alert-title">添加公共服务页</h3>
                        <button type="button" class="add-public-close close pull-right">&times;</button>
                    </div>
                    <form class="form-horizontal" >
                    <div class="panel-body">
                        <input type="hidden" value="" class="public-id">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">页面名称: </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control sm public-title" name="title" value="" autocomplete="off">
                                <p class="help-block">请输入1-8个汉字、字母或数字作为页面名称</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">页面图标: </label>
                            <div class="col-sm-9">
                                <input type="file" name="icon" class="public-icon">
                                <input type="hidden" class="" name="" value="">
                                <!-- <p class="help-block">请上传<span class=""></span>比例的JPG或PNG格式图片作为图标</p> -->
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <img src="" style="width:50px;height:50px;" class="img-rounded public-img">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">选择服务类别: </label>
                            <div class="col-sm-9">
                                <select name="type" class="form-control public-type">
                                    <option value="cater">餐饮美食</option>
                                    <option value="hotel">酒店宾馆</option>
                                    <option value="shopping">商场超市</option>
                                    <option value="life">生活服务</option>
                                    <option value="beauty">美容美发</option>
                                    <option value="hospital">医疗机构</option>
                                    <option value="scope">旅游景点</option>
                                    <option value="education">教育培训</option>
                                    <option value="house">楼宇大厦</option>
                                    <option value="enterprise">公司企业</option>
                                    <option value="">所有</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">输入服务词: </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control sm public-keyword" name="keyword" value="" placeholder="">
                                <p class="help-block">服务词如：中餐或商家名称等</p>
                            </div>
                        </div>
                    </div>
                    </form>
                    <div class="panel-footer text-right">
                        <input type="button" class="btn btn-primary btn-sm disabled save-public" value="确定" >  
                        <input type="button" class="btn btn-primary btn-sm edit-public" value="确定" >  
                        <button type="button" class="btn btn-default btn-sm add-public-close">取 消</button>
                    </div>
                </div>
            </div>

            <!-- wap地址 -->
            <div class="wap-public">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">WAP地址</h3>
                        <button type="button" class="wap-public-close close pull-right">&times;</button>
                    </div>
                    <form class="form-horizontal">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="" class="col-sm-1 control-label">URL: </label>
                            <div class="col-sm-11">
                                <!-- <input type="text" class="form-control sm public-url" name="" value="http://wsh.appbyme.com/jsdflwehgfhvnr" style="width:420px"> -->
                                <p class="form-control-static public-url"  style="width:420px"></p>
                            </div>
                        </div>
<!--                         <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Token: </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control sm" name="" value="Yunodsf'jodsfjkldvlendcn" placeholder="">
                                <p class="help-block" style="color:red">温馨提示：URL地址可填写在自定义管理中的WAP外部链接
                  URL+Token可填写在已认证的微信公众号子菜单</p>
                            </div>
                        </div> -->
                    </div>
                    <div class="panel-footer text-right">
                        <button type="button" class="btn btn-default btn-sm wap-public-close">取 消</button>
                    </div>
                    </form>
                </div>
            </div>

            <ul id="wshTab" class="nav nav-tabs">
<!--                 <li class="active">
                    <a href=".wsh-index" data-toggle="tab">微生活首页</a>
                </li>  -->               
                <li class="active">
                    <a href=".wsh-public" data-toggle="tab">公共服务</a>
                </li>                
<!--                 <li class="">
                    <a href="#wsh-merch-management" data-toggle="tab">商户管理</a>
                </li> -->
<!--                 <li class="">
                    <a href="#wsh-data-statistical" data-toggle="tab">数据统计</a>
                </li> -->
<!--                 <li class="dropdown">
                    <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown">Java <b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1">
                        <li><a href="#jmeter" tabindex="-1" data-toggle="tab">jmeter</a></li>
                        <li><a href="#ejb" tabindex="-1" data-toggle="tab">ejb</a></li>
                    </ul>
                </li> -->
            </ul>

            <div id="wshTabContent" class="tab-content">

<!--                 <div class="tab-pane fade in active wsh-right-border wsh-index">

                    <div class="module-list">
                        <img class="img-thumbnail" src="<?php echo $this->rootUrl.'/images/admin/module-default.png'; ?>">
                        <div class="module-title">微生活首页</div>
                        <div class="module-edit">
                            <span>
                                <a class="wap-public-btn" data-url="http://wsh.appbyme.com/">WAP地址</a>
                            </span>
                        </div>
                    </div>
                </div> -->

                <div class="tab-pane fade in active wsh-right-border wsh-public">

                    <div class="module-list">
                        <img class="img-thumbnail" src="<?php echo !empty($module['icon']) ? $module['icon'] : $this->rootUrl.'/images/admin/module-default.png'; ?>">
                        <div class="module-title">公共服务首页</div>
                        <div class="module-edit">
                            <span>
                                <a class="wap-public-btn" data-url="http://wsh.appbyme.com">WAP地址</a>
                                <!-- <a class="wap-public-btn" data-url="http://wsh.appbyme.com/">WAP地址</a> -->
                            </span>
                        </div>
                    </div>

                    <?php foreach($moduleList as $module): ?>
                    <?php 
                        $url = "http://wsh.appbyme.com/index.php?r=service/list&q=";
                        if (isset($module['keyword']) && $module['keyword'] != '') {
                            $url .= $module['keyword'];
                        }

                        if (isset($module['type']) && $module['type'] != '') {
                            $url .= '&filter=industry_type:'.$module['type'].'|sort_name:distance';
                        }
                    ?>
                    <div class="module-list">
                        <img class="img-rounded" src="<?php echo !empty($module['icon']) ? $module['icon'] : $this->rootUrl.'/images/admin/module-default.png'; ?>">
                        <div class="module-title"><?php echo isset($module['title'])  ? $module['title'] : ''; ?></div>
                        <div class="module-edit">
                            <span><a class="edit-public-btn" data-id=<?php echo $module['id']; ?>>编辑</a></span> 
                            <span>
                                <a class="wap-public-btn" data-url="<?php echo $url; ?>">WAP地址</a>
                            </span>
                            <span><a href="<?php echo $this->dzRootUrl; ?>/mobcent/app/web/index.php?r=admin/wshdiy/delpublic&id=<?php echo $module['id']; ?>" onclick="return confirm('真的要删除吗？')">删除</a></span>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="add-public-btn">
                        <img src="<?php echo $this->rootUrl; ?>/images/admin/module-add.png">
                        <div><a onclick=switchCovering('add-public')>添加公共服务页</a></div>
                    </div>
                </div>
<!--                 <div class="tab-pane fade wsh-right-border" id="wsh-merch-management">
                    商户管理
                </div>
                <div class="tab-pane fade wsh-right-border" id="wsh-data-statistical">
                    数据统计
                </div> -->
            </div>

        </div>
    </div>
</body>
<script type="text/javascript">
    var dzRootUrl = "<?php echo $this->dzRootUrl; ?>";
</script>