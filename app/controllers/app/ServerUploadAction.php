<?php

/**
 * 服务器上传接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ServerUploadAction extends MobcentAction
{
    public function run($type='add_certfile_apns')
    {
        $res = $this->initWebApiArray();
        
        $res = $this->_doUpload($res, $type);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _doUpload($res, $type)
    {
        $path = MOBCENT_UPLOAD_PATH;
        $certfileAPNs = $path.'/appbyme_push.pem';
        if ($type == 'add_certfile_apns') {
            if (UploadUtils::makeBasePath($path)) {
                if (!empty($_FILES) && count($_FILES) && is_uploaded_file($_FILES['file']['tmp_name']) && !$_FILES['file']['error']) {
                    FileUtils::saveFile($certfileAPNs, file_get_contents($_FILES['file']['tmp_name']));
                    AppbymeConfig::setAPNsCertfilePassword($_POST['certfile_apns_passphrase']);
                } else {
                    $res = $this->makeErrorInfo($res, WebUtils::t('上传失败'));
                }
            }
        } else if ($type == 'del_certfile_apns') {
            FileUtils::safeDeleteFile($certfileAPNs);
        }

        return $res;
    }
}