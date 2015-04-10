<?php 

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class RewardController extends AdminController{

    const ACTIVITY_TYPE = 'invite';
    public function actionRewardList() {
        $rewardList = AppbymeActivityModel::getActivityInvite();
        $this->renderPartial('rewardlist', array('rewardList' => $rewardList));
    }

    public function actionRewardAdd() {
        if(!empty($_POST)) {
            $sponsor = isset($_POST['sponsor']) ? $_POST['sponsor'] : '';
            $startTime = isset($_POST['startTime']) ? strtotime($_POST['startTime']) : '';
            $stopTime = isset($_POST['stopTime']) ? strtotime($_POST['stopTime']) : '';
            $firstReward = isset($_POST['firstReward']) ? $_POST['firstReward'] : '';
            $inviteReward = isset($_POST['inviteReward']) ? $_POST['inviteReward'] : '';
            $exchangeMin = isset($_POST['exchangeMin']) ? $_POST['exchangeMin'] : '';
            // $exchangeType = isset($_POST['exchangeType']) ? $_POST['exchangeType'] : '';
            $virtualName = isset($_POST['virtualName']) ? $_POST['virtualName'] : '';
            $exchangeRatio = isset($_POST['exchangeRatio']) ? $_POST['exchangeRatio'] : '';
            $limitUser = isset($_POST['limitUser']) ? $_POST['limitUser'] : '';
            $limitDevice = isset($_POST['limitDevice']) ? $_POST['limitDevice'] : '';
            $limitTime = isset($_POST['limitTime']) ? $_POST['limitTime'] : '';
            $limitDays = isset($_POST['limitDays']) ? $_POST['limitDays'] : '';
            $limitNum = isset($_POST['limitNum']) ? $_POST['limitNum'] : '';
            $activityRule = isset($_POST['activityRule']) ? $_POST['activityRule'] : '';
            $shareAppUrl = isset($_POST['shareAppUrl']) ? $_POST['shareAppUrl'] : '';

            if ($limitDays == '') {
                $limitDays = abs($startTime - $stopTime)/3600/24;
            }

            $insertReward = array(
                'start_time' => $startTime,
                'stop_time' => $stopTime,
                'type' => self::ACTIVITY_TYPE,
                'is_run' => 1,
            );

            AppbymeActivityModel::insertActivity($insertReward);

            $activityId = mysql_insert_id();

            $insertRewardInvite = array(
                'activity_id' => $activityId,
                'sponsor' => WebUtils::t($sponsor),
                'start_time' => $startTime,
                'stop_time' => $stopTime, 
                'first_reward' => $firstReward,
                'invite_reward' => $inviteReward,
                'exchange_min' => $exchangeMin,
                // 'exchange_type' => $exchangeType,
                'virtual_name' => WebUtils::t($virtualName),
                'exchange_ratio' => $exchangeRatio,
                'limit_user' => $limitUser,
                'limit_device' => $limitDevice,
                'limit_time' => $limitTime,
                'limit_days' => $limitDays,
                'limit_num' => $limitNum,
                'activity_rule' => $activityRule,
                'share_appurl' => $shareAppUrl,
            );

            AppbymeActivityInviteModel::insertActivityInvite($insertRewardInvite);
            header('location:'.$this->rootUrl.'/index.php?r=admin/reward/rewardlist');
        }
        $this->renderPartial('rewardadd');
    }

    public function actionRewardEdit($id=0) {

        if (!empty($_POST)) {
            $sponsor = isset($_POST['sponsor']) ? $_POST['sponsor'] : '';
            $startTime = isset($_POST['startTime']) ? strtotime($_POST['startTime']) : '';
            $stopTime = isset($_POST['stopTime']) ? strtotime($_POST['stopTime']) : '';
            $firstReward = isset($_POST['firstReward']) ? $_POST['firstReward'] : '';
            $inviteReward = isset($_POST['inviteReward']) ? $_POST['inviteReward'] : '';
            $exchangeMin = isset($_POST['exchangeMin']) ? $_POST['exchangeMin'] : '';
            // $exchangeType = isset($_POST['exchangeType']) ? $_POST['exchangeType'] : '';
            $virtualName = isset($_POST['virtualName']) ? $_POST['virtualName'] : '';
            $exchangeRatio = isset($_POST['exchangeRatio']) ? $_POST['exchangeRatio'] : '';
            $limitUser = isset($_POST['limitUser']) ? $_POST['limitUser'] : '';
            $limitDevice = isset($_POST['limitDevice']) ? $_POST['limitDevice'] : '';
            $limitTime = isset($_POST['limitTime']) ? $_POST['limitTime'] : '';
            $limitDays = isset($_POST['limitDays']) ? $_POST['limitDays'] : '';
            $limitNum = isset($_POST['limitNum']) ? $_POST['limitNum'] : '';
            $activityRule = isset($_POST['activityRule']) ? $_POST['activityRule'] : '';
            $shareAppUrl = isset($_POST['shareAppUrl']) ? $_POST['shareAppUrl'] : '';

            if ($limitDays == '') {
                $limitDays = abs($startTime - $stopTime)/3600/24;
            }

            $updateRewardInvite = array(
                'sponsor' => WebUtils::t($sponsor),
                'start_time' => $startTime,
                'stop_time' => $stopTime, 
                'first_reward' => $firstReward,
                'invite_reward' => $inviteReward,
                'exchange_min' => $exchangeMin,
                // 'exchange_type' => $exchangeType,
                'virtual_name' => WebUtils::t($virtualName),
                'exchange_ratio' => $exchangeRatio,
                'limit_user' => $limitUser,
                'limit_device' => $limitDevice,
                'limit_time' => $limitTime,
                'limit_days' => $limitDays,
                'limit_num' => $limitNum,
                'activity_rule' => $activityRule,
                'share_appurl' => $shareAppUrl,
            );
            $activityId = $_POST['activityId'];
            AppbymeActivityInviteModel::updateActivityInvite($activityId, $updateRewardInvite);
            $cacheKey = CacheUtils::getActivityInviteKey(array('invite', $activityId));
            Yii::app()->cache->delete($cacheKey);
            header('location:'.$this->rootUrl.'/index.php?r=admin/reward/rewardlist');
        }
        $inviteInfo = AppbymeActivityInviteModel::getActivityInviteById($id);
        $this->renderPartial('rewardedit', array('inviteInfo' => $inviteInfo));    
    }

    public function actionRewardView($id) {
        $inviteInfo = AppbymeActivityInviteModel::getActivityInviteById($id);
        $this->renderPartial('rewardview', array('inviteInfo' => $inviteInfo));
    }    

    public function actionRewardCount($show=0,$type='', $username='', $page=1, $pageSize=10) {
        if (!$show) {
            $rootUrl = $this->dzRootUrl;
            $searchRes = AppbymeActivityInviteUser::search($type, $username, $page, $pageSize, $rootUrl);
        }

        $this->renderPartial('rewardcount', array('searchList' => $searchRes['searchList'], 'page'=>$searchRes['page'], 'maxPage' => $searchRes['maxPage'], 'count' => $searchRes['count'], 'prev' => $searchRes['prev'], 'next' => $searchRes['next']));
    }

    // 管理后台站长兑换
    public function actionRewardExchange($uid, $exchangeNum) {
        $res = WebUtils::initWebApiResult();
        $res['errCode'] = 1;
        $exchangeInfo = AppbymeActivityInviteUser::getExchangeInfo($uid);
        if ($exchangeNum > $exchangeInfo['available_reward']) {
            $res['errCode'] = 0;
            $res['errMsg'] = '请输入正确的兑换金额';
        }

        if (!$exchangeInfo['exchange_status']) {
            $res['errCode'] = 0;
            $res['errMsg'] = '用户没有申请兑换！';
        }

        $exchangeInfo = AppbymeActivityInviteUser::execExchange($uid, $exchangeNum);
        // var_dump($exchangeInfo);die;
        // mobcent::dumpSql();
        if (!$exchangeInfo) {
            $res['errCode'] = 0;
            $res['errMsg'] = '兑换失败！';   
        }
        echo WebUtils::outputWebApi($res, '', true);
    }

    // 管理后台站长标记用户
    public function actionFlagUser($uid) {
        $res = WebUtils::initWebApiResult();
        $res['errCode'] = 1;
        $data = array('flag' => 1);
        $flagInfo = AppbymeActivityInviteUser::flagUser($uid, $data);
        if (!$flagInfo) {
            $res['errCode'] = 0;
            $res['errMsg'] = '标记失败！';               
        }
        echo WebUtils::outputWebApi($res, '', true);
    }   
}

?>