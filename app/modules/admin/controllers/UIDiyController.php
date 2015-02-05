<?php

/**
 * UI Diy 控制器
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class UIDiyController extends AdminController
{
    public $navItemIconBaseUrlPath = '';
    public $componentFastpostIconBaseUrlPath = '';
    public $componentDiscoverIconBaseUrlPath = '';
    public $componentTopbarIconBaseUrlPath = '';

    public function init()
    {
        parent::init();

        $this->navItemIconBaseUrlPath = $this->rootUrl . '/images/admin/icon1';
        $this->componentFastpostIconBaseUrlPath = $this->rootUrl . '/images/admin/icon2';
        $this->componentDiscoverIconBaseUrlPath = $this->rootUrl . '/images/admin/icon3';
        $this->componentTopbarIconBaseUrlPath = $this->rootUrl . '/images/admin/topbar';
    }

    public function getComponentIconUrl($icon)
    {
        if (strpos($icon, AppbymeUIDiyModel::COMPONENT_ICON_DISCOVER_DEFAULT) !== false) {
            return $this->componentDiscoverIconBaseUrlPath.'/'.$icon.'.png';
        } else if (strpos($icon, AppbymeUIDiyModel::COMPONENT_ICON_FASTPOST) !== false) {
            return $this->componentFastpostIconBaseUrlPath.'/'.$icon.'_n.png';
        } else if (strpos($icon, AppbymeUIDiyModel::COMPONENT_ICON_TOPBAR) !== false) {
            return $this->componentTopbarIconBaseUrlPath.'/'.$icon.'_n.png';
        } else {
            return $icon;    
        }
    }

    public function actionIndex()
    {
        $newsModules = AppbymePoralModule::getModuleList();
        $forumList = ForumUtils::getForumListForHtml();

        $navInfo = AppbymeUIDiyModel::getNavigationInfo(true);
        $tempModules = AppbymeUIDiyModel::getModules(true);
        // 初始化默认配置
        if (empty($navInfo) || empty($navInfo['navItemList']) || empty($tempModules)) {
            $navInfo = AppbymeUIDiyModel::initNavigation();
            $tempModules = AppbymeUIDiyModel::initModules();
        }
        
        // 必须存在发现导航
        $hasDiscoverNavItem = false;
        foreach ($navInfo['navItemList'] as $navItem) {
            if ($navItem['moduleId'] == AppbymeUIDiyModel::MODULE_ID_DISCOVER) {
                $hasDiscoverNavItem = true;
                break;
            }
        }
        !$hasDiscoverNavItem && array_unshift($navInfo['navItemList'], AppbymeUIDiyModel::initNavItemDiscover());

        // 必须存在发现和快发模块
        $isFindDiscover = $isFindFastpost = false;
        $discoverModule = AppbymeUIDiyModel::initDiscoverModule();
        $fastpostModule = AppbymeUIDiyModel::initFastpostModule();

        $modules = array();
        foreach ($tempModules as $module) {
            switch ($module['id']) {
                case AppbymeUIDiyModel::MODULE_ID_DISCOVER:
                    if (!$isFindDiscover) {
                        $isFindDiscover = true;
                        $discoverModule = $module;
                    }
                    break;
                case AppbymeUIDiyModel::MODULE_ID_FASTPOST:
                    if (!$isFindFastpost) {
                        $isFindFastpost = true;
                        $fastpostModule = $module;
                    }
                    break;
                default:
                    $modules[] = $module;
                    break;
            }
        }
        array_unshift($modules, $discoverModule, $fastpostModule);

        // 检测浏览器信息
        $mobileDetect = new Mobile_Detect();
        $browserInfo = $mobileDetect->version('Chrome');

        $this->renderPartial('index', array(
            'navInfo' => $navInfo,
            'modules' => $modules,
            'newsModules' => $newsModules,
            'forumList' => $forumList,
            'browserInfo' => $browserInfo,
            'appLevel' => AppUtils::getAppLevel(),
            'topicTypeSortInfos' => $this->_getTopicTypeSortInfos(),
        ));
    }

    public function actionSaveNavInfo($navInfo, $isSync=0)
    {
        $res = WebUtils::initWebApiResult();

        $navInfo = WebUtils::jsonDecode($navInfo);
        AppbymeUIDiyModel::saveNavigationInfo($navInfo, true);
        $isSync && AppbymeUIDiyModel::saveNavigationInfo($navInfo);

        echo WebUtils::outputWebApi($res, 'utf-8', false);
    }

    public function actionSaveModules($modules, $isSync=0)
    {
        $res = WebUtils::initWebApiResult();

        $modules = WebUtils::jsonDecode($modules);
        AppbymeUIDiyModel::saveModules($modules, true);

        if ($isSync) {
            $tempModules = array();
            foreach ($modules as $module) {
                $module['leftTopbars'] = $this->_filterTopbars($module['leftTopbars']);
                $module['rightTopbars'] = $this->_filterTopbars($module['rightTopbars']);
                
                $tempComponentList = array();
                foreach ($module['componentList'] as $component) {
                    $component = $this->_filterComponent($component);
                    if ($module['type'] == AppbymeUIDiyModel::MODULE_TYPE_SUBNAV) {
                        if ($component['title'] != '') {
                            $tempComponentList[] = $component;
                        }
                    } else {
                        $tempComponentList[] = $component;
                    }
                }                    
                $module['componentList'] = $tempComponentList;

                $tempModules[] = $module;
            }
            AppbymeUIDiyModel::saveModules($tempModules);
        }

        echo WebUtils::outputWebApi($res, 'utf-8', false);
    }

    public function actionInit()
    {
        $res = WebUtils::initWebApiResult();

        AppbymeUIDiyModel::saveNavigationInfo(AppbymeUIDiyModel::initNavigation(), true);
        AppbymeUIDiyModel::saveModules(AppbymeUIDiyModel::initModules(), true);

        echo WebUtils::outputWebApi($res, 'utf-8', false);
    }

    // render 模块手机视图
    public function actionModulemobileui($module) 
    {   
        $module = WebUtils::jsonDecode($module);
        $this->renderPartial('module_mobile_ui', array(
            'module' => $module,
        ));
    }

    // render 组件手机视图
    public function actionComponentmobileui($component) 
    {   
        $component = rawurldecode($component);
        $component = WebUtils::jsonDecode($component);
        $this->renderPartial('component_mobile_ui', array(
            'component' => $component,
        ));
    }

    // 导入配置
    public function actionImportConfig() 
    {
        $res = WebUtils::initWebApiResult();

        $errMsgs = array(
            0 => '导入配置成功',
            1 => '导入配置失败',
        );
        $res = WebUtils::makeWebApiResult($res, 1, $errMsgs[1]);

        if (!empty($_FILES) && count($_FILES) && is_uploaded_file($_FILES['file']['tmp_name']) && !$_FILES['file']['error']) {
            $config = $this->_decodeConfig(file_get_contents($_FILES['file']['tmp_name']));
            if (!empty($config)) {
                AppbymeUIDiyModel::saveNavigationInfo($config['navigation'], true);
                AppbymeUIDiyModel::saveModules($config['moduleList'], true);
                $res = WebUtils::makeWebApiResult($res, 0, $errMsgs[0]);
            }
        }

        WebUtils::outputWebApi($res, 'utf-8');
    }

    // 导出配置
    public function actionExportConfig() 
    {   
        header('Pragma: public');
        header('Expires: 0'); 
        header('Accept-Ranges: bytes'); 
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0, max-age=0');  
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header(sprintf('Content-Disposition: attachment; filename="%s"', 'appbyme_uidiy.config'));

        file_put_contents('php://output', $this->_encodeConfig());
    }

    private function _encodeConfig() {
        $config = array(
            'version' => AppbymeUIDiyModel::CONFIG_VERSION,
            'dataChecksum' => '',
            'data' => array(
                'navigation' => AppbymeUIDiyModel::getNavigationInfo(true),
                'moduleList' => AppbymeUIDiyModel::getModules(true),
            ),
        );
        $config['dataChecksum'] = md5(WebUtils::jsonEncode($config['data'], 'utf-8'));
        $config = (string)WebUtils::jsonEncode($config, 'utf-8');
        return base64_encode($config);
    }

    private function _decodeConfig($config) {
        $res = array();

        $tmpConfig = base64_decode($config);
        $tmpConfig = WebUtils::jsonDecode($tmpConfig);
        if (isset($tmpConfig['data']) && isset($tmpConfig['dataChecksum']) && 
            $tmpConfig['dataChecksum'] == md5(WebUtils::jsonEncode($tmpConfig['data'], 'utf-8'))) {
            $res = $tmpConfig['data'];
        }

        return $res;
    }

    private function _filterTopbars($topbars)
    {
        $tempTopbars = array();
        foreach ($topbars as $topbar) {
            $topbar = $this->_filterComponent($topbar);
            $topbar['type'] != AppbymeUIDiyModel::COMPONENT_TYPE_EMPTY && $tempTopbars[] = $topbar;
        }
        return $tempTopbars;
    }

    private function _filterComponent($component) 
    {
        loadcache('forums');
        global $_G;
        $forums = $_G['cache']['forums'];

        $tempComponent = $component;

        // 转换fastpostForumIds结构
        $tempFastpostForumIds = array();
        foreach ($component['extParams']['fastpostForumIds'] as $fid) {
            $tempFastpostForumIds[] = array(
                'fid' => $fid,
                'title' => WebUtils::u($forums[$fid]['name']),
            );
        }
        $tempComponent['extParams']['fastpostForumIds'] = $tempFastpostForumIds;

        // 转换componentList结构
        $tempComponentList = array();
        foreach ($component['componentList'] as $subComponent) {
            if (!$subComponent['extParams']['isHidden']) {
                $tempComponentList[] = $this->_filterComponent($subComponent);
            }
        }
        $tempComponent['componentList'] = $tempComponentList;
        
        return $tempComponent;
    }

    private function _getTopicTypeSortInfos() {
        $infos = array();

        $fields = DbUtils::getDzDbUtils(true)->queryAll('
            SELECT fid, threadtypes, threadsorts
            FROM %t
            WHERE fid IN (%n)
            ',
            array('forum_forumfield', DzForumForum::getFids())
        );
        foreach ($fields as $field) {
            if (!empty($field)) {
                $info = array('fid' => (int)$field['fid'], 'types' => array(), 'sorts' => array());

                $types = unserialize($field['threadtypes']);
                if (!empty($types['types'])) {
                    foreach ($types['types'] as $key => $value) {
                        // 控制管理组专用
                        // if ($types['moderators'][$key] == 1) {
                            // continue;
                        // }
                        $info['types'][] = array(
                            'id' => $key,
                            'title' => WebUtils::emptyHtml($value),
                        );
                    }
                }
                $sorts = unserialize($field['threadsorts']);
                if (!empty($sorts['types'])) {
                    foreach ($sorts['types'] as $key => $value) {
                        $info['sorts'][] = array(
                            'id' => $key,
                            'title' => WebUtils::emptyHtml($value),
                        );
                    }
                }

                $infos[$info['fid']] = $info;
            }
        }
        return $infos;
    }

    /**
     * 上传图片
     * 
     * @author HanPengyu
     * @access public
     *
     * @return mixed 返回状态码和信息.
     */
    public function actionUploadIcon($type='uidiy') {

        $res = WebUtils::initWebApiResult();

        // 没有上传的文件
        if(empty($_FILES)) {
            self::makeResponse(0, '没有上传的文件,或者选择的文件太大！');
        }

        // 创建放置图片的文件夹
        $date = date('Ym/d', time());
        $path = MOBCENT_UPLOAD_PATH.'/'.$type.'/'.$date;

        if ( UploadUtils::makeBasePath($path) == '') {
            self::makeResponse(0, '上传目录不可写！');
        }

        foreach ($_FILES as $file) {

            $file['name']  = strip_tags($file['name']);
            $ext = FileUtils::getFileExtension($file['name'], 'jpg');

            // 检测
            $imageRes = $this->checkUpload($res, $file);

            if (!$imageRes['errCode']) {
                self::makeResponse(0, $imageRes['errMsg']);
            }

            $saveName = FileUtils::getRandomUniqueFileName($path);
            $fileName = $saveName.'.'.$ext;

            if (!move_uploaded_file($file['tmp_name'], $fileName)) {
                self::makeResponse(0, '上传图片失败！');
            }

            $fileName = $this->dzRootUrl.'/data/appbyme/upload/'.$type.'/'.$date.'/'.basename($fileName);
            ImageUtils::getThumbImageEx($fileName, 10, false, false, true);
            self::makeResponse(1, $fileName);
        }

    }

    /**
     * 检测上传相关项
     * 
     * @param mixed $res  初始化数组.
     * @param mixed $file 上传的单个文件数组信息.
     *
     * @return mixed array.
     */
    public function checkUpload($res, $file) {

        // 文件上传失败，捕获错误代码
        if ($file['error']) {
            $res['errCode'] = 0;
            $res['errMsg'] = self::error($file['error']);
            return $res;
        }

        // 无效上传
        $file['name'] = strip_tags($file['name']);
        if (empty($file['name'])) {
            $res['errCode'] = 0;
            $res['errMsg'] = '未知上传错误！';
            return $res;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            $res['errCode'] = 0;
            $res['errMsg'] = '非法上传文件';
            return $res;
        }

        // 检查文件大小
        $maxSize = 2000000;
        if ($file['size'] > $maxSize || $file['size'] == 0) {
            $res['errCode'] = 0;
            $res['errMsg'] = '上传文件大小不符！';
            return $res;
        }

        // 检查文件Mime类型
        $mime = $file['type'];
        $allowMime = array('image/png', 'image/jpeg');
        if (!in_array(strtolower($mime), $allowMime)) {
            $res['errCode'] = 0;
            $res['errMsg'] = '上传文件MIME类型不允许！';
            return $res;
        }

        // 检查文件后缀
        $ext = FileUtils::getFileExtension($file['name'], 'jpg');
        $allowExt = array('jpg', 'png', 'jpeg');
        if (!in_array(strtolower($ext), $allowExt)) {
            $res['errCode'] = 0;
            $res['errMsg'] = '上传文件后缀不允许!';
            return $res;
        }

        // 通过检测
        $res['errCode'] = 1;
        return $res;
    }

    /**
     * 通过php上传的错误码获取具体的错误信息
     * 
     * @param mixed $errNo 上传错误码.
     *
     * @return mixed string.
     */
    public static function error($errNo) {
        switch ($errNo) {
            case 1:
                $msg = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！';
                break;
            case 2:
                $msg = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值！';
                break;
            case 3:
                $msg = '文件只有部分被上传！';
                break;
            case 4:
                $msg = '没有文件被上传！';
                break;
            case 6:
                $msg = '找不到临时文件夹！';
                break;
            case 7:
                $msg = '文件写入失败！';
                break;
            default:
                $msg = '未知上传错误！';
        }
        return $msg;
    }


    /**
     * 删除指定的图片
     * 
     * @param mixed $fileName 文件的名字，是带有全路径的.
     *
     * @return mixed 返回状态码和信息.
     *
     */
    public function actionDelIcon($fileName) {
        $basename = str_replace($this->dzRootUrl.'/data/appbyme/upload/', '', $fileName);
        $fileName = MOBCENT_UPLOAD_PATH.'/'.$basename;
        if (!file_exists($fileName)) {
            self::makeResponse(0, '图片不存在！');
        }

        if (!unlink($fileName)) {
            self::makeResponse(0, '图片删除失败！');
        }
        self::makeResponse(1, '图片删除成功！');
    }

    public static function makeResponse($errCode=1, $errMsg='') {
        $res = WebUtils::initWebApiResult();
        $res['errCode'] = $errCode;
        $res['errMsg'] = $errMsg;
        $res['data'] = array();
        WebUtils::outputWebApi($res, 'utf-8', true);
    }
}