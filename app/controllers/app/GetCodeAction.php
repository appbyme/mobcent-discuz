<?php 
        
/**
 * 获取手机短信验证码接口
 *
 * @author HanPengyu
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class GetCodeAction extends MobcentAction {

    public function run($type='mobile', $mobile='', $act='register') {
        $res = $this->initWebApiArray();
        $res = $this->getSms($res, $type, $mobile, $act);
        echo WebUtils::outputWebApi($res, '', true);
    }

    public function getSms($res, $type, $mobile, $act) {

        //主帐号,对应官网开发者主账号下的 ACCOUNT SID
        $accountSid = WebUtils::getDzPluginAppbymeAppConfig('yun_accountsid');

        //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
        $accountToken = WebUtils::getDzPluginAppbymeAppConfig('yun_authtoken');

        //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
        //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
        $appId = WebUtils::getDzPluginAppbymeAppConfig('appbyme_appid');

        // 主账号里面的模板id
        $templateId = WebUtils::getDzPluginAppbymeAppConfig('yun_moduleid');
        // $templateId = 1;

        if ($accountSid == '' || $accountToken == '' || $appId == '' || $templateId == '') {
            return $this->makeErrorInfo($res, 'mobcent_yun_config_error');
        }

        //请求端口，生产环境和沙盒环境一致
        $serverPort='8883';

        //请求地址
        //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
        //生产环境（用户应用上线使用）：app.cloopen.com
        $serverIP='sandboxapp.cloopen.com';

        //REST版本号，在官网文档REST介绍中获得。
        $softVersion='2013-12-26';

        // 验证码
        $code = self::getRandomCode();

        //code的有效时间
        $activeTime = 2;
        
        $params = array(
            'serverIP' => $serverIP,
            'serverPort' => $serverPort,
            'softVersion' => $softVersion,
            'accountSid' => $accountSid,
            'accountToken' => $accountToken,
            'appId' => $appId,
            'action' => $act,
            'type' => $type,
        );

        //手机号码，替换内容数组，模板ID
        $res = $this->sendTemplateSMS($res, $mobile,array($code,$activeTime),$templateId, $params); 
        return $res;
    }

    /**
    * 发送模板短信
    * @param to 手机号码集合,用英文逗号分开
    * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
    * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
    *
    */       
    private function  sendTemplateSMS($res, $to,$datas,$tempId, $params) {
        extract($params);
         // 初始化REST SDK
         // global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion,$db;
         $rest = new RestSmsSDK($serverIP,$serverPort,$softVersion);
         $rest->setAccount($accountSid,$accountToken);
         $rest->setAppId($appId);
         if($type == 'mobile'){//手机注册
             // 发送模板短信
             //echo "Sending TemplateSMS to $to <br/>";
             if(!$to){
                 // $res = WebUtils::makeErrorInfo_oldVersion($res, 'mobile_empty');
                 return $this->makeErrorInfo($res, 'mobcent_mobile_empty');
             }
             if(!preg_match('/^1(3|5|8|7)\d{9}$/',$to)){ //^(((d{2,3}))|(d{3}-))?13d{9}$
                 // $res = WebUtils::makeErrorInfo_oldVersion($res,'mobile_error');
                 return $this->makeErrorInfo($res, 'mobcent_mobile_error');
             }
            if($action == 'register'){//注册验证手机号是否唯一
                // $mobileInfo = $db->get_one("SELECT * FROM pw_appbyme_sendsms WHERE mobile = " . S::sqlEscape($to) ." AND uid > 0");
                 $bindInfo = UserUtils::checkMobile($to);
                 if($bindInfo){
                    // $res = WebUtils::makeErrorInfo_oldVersion($res,'mobile_repeat');
                     return $this->makeErrorInfo($res, 'mobcent_mobile_repeat');
                 }
             }
             
             $result = $rest->sendTemplateSMS($to,$datas,$tempId);
             if($result == NULL ) {
                 // $res = WebUtils::makeErrorInfo_oldVersion($res,'result_error');
                 return $this->makeErrorInfo($res, 'mobcent_result_error');
                 //echo "result error!";
                 //break;
             }

             if($result->statusCode!=0) {
                 $res['rs'] = 0;
                 $res['head']['alert'] = 1;
                 $res['errcode'] = $res['head']['errCode'] = $result->statusCode;
                 $res['head']['errInfo'] = $result->statusMsg;
                 //echo "error code :" . $result->statusCode . "<br>";
                 //echo "error msg :" . $result->statusMsg . "<br>";
                 //TODO 添加错误处理逻辑
                 // return $this->makeErrorInfo($res, 'mobcent_result_error');
             }else{
                // echo "Sendind TemplateSMS success!<br/>";
                 // 获取返回信息
                $smsmessage = $result->TemplateSMS;

                $time = strtotime($smsmessage->dateCreated);
                $inserArray = array(
                    'id' => '',
                    'mobile' => $to,
                    'code' => $datas[0],
                    'time' => $time,
                    'uid' => 0
                );

                $mobileInfo = AppbymeSendsms::getMobileUidInfo($to);
                if ($mobileInfo) {
                    $updataArr = array('time' => $time, 'code' => $datas[0]);
                    AppbymeSendsms::updateMobile($to, $updataArr);
                } else {
                    AppbymeSendsms::insertMobile($inserArray);
                }
             }
             return $res;
         }else{//pc注册

         }
    }

    /**
     * 生成随机的手机验证码
     * 
     * @param int $len 验证码的长度
     *
     * @access public
     * @static
     *
     * @return string.
     */
    public static function getRandomCode($len=6) {
        $randomArr = range(0, 9);
        $tempName = '';
        for ($i = 0; $i < $len; $i++) {
            $tempName .= $randomArr[array_rand($randomArr)];
        }
        return $tempName;
    }

}


?>