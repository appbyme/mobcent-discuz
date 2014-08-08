<?php

/**
 * 主题 工具类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class TopicUtils {

   public static function getActivityInfo($tid) {
        $activity = array('image' => '', 'summary' => '', 'content' => array(), 'action' => array(), 'applyList' => array());

        $activityInfo = ForumUtils::getTopicActivityInfo($tid);
        if (!empty($activityInfo)) {
            $activity['image'] = WebUtils::getHttpFileName($activityInfo['thumb']);
            $activity['summary'] = self::_getActivitySummary($activityInfo);
            $activity['action'] = self::_getActivityAction($activityInfo);
            $activity['applyList'] = self::_getActivityApplyList($activityInfo);
            $activity['applyListVerified'] = self::_getActivityApplyListVerified($activityInfo);
        }

        return $activity;
    }

    private static function _getActivitySummary($activity) {
        $startTime = $activity['starttimefrom'] . ($activity['starttimeto'] ? WebUtils::t(' 至 ').$activity['starttimeto'].WebUtils::t(' 商定') : '');
        $gender = WebUtils::t($activity['gender'] == 1 ? '男' : ($activity['gender'] == 2 ? '女' : '不限'));
        $cost = WebUtils::t($activity['cost'] ? "每人花销:\t{$activity['cost']} 元\n" : '');
        $allApplyNums = $activity['mobcent']['allApplyNums'];
        $leftNums = WebUtils::t($activity['number'] ? "剩余名额:\t{$activity['mobcent']['leftNums']} 人\n" : '');
        $expiration = WebUtils::t($activity['expiration'] ? "报名截止:\t{$activity['expiration']}" : '');
        $res = sprintf(WebUtils::t(
            "活动类型:\t%s\n" . 
            "开始时间:\t%s\n" .
            "活动地点:\t%s\n" .
            "性别:\t%s\n" .
            "%s" .
            "已报名人数:\t%s\n" .
            "%s" . 
            "%s"
            ) ,    
            $activity['class'], $startTime, $activity['place'], 
            $gender, $cost, $allApplyNums, $leftNums, $expiration
        );
        return $res;
    }

    private static function _getActivityAction($activity) {
        $action = null;
        
        $isVerified = $activity['mobcent']['isVerified'];
        $applied = $activity['mobcent']['applied'];
        $isActivityClose = $activity['mobcent']['isActivityClose'];
        $leftNums = $activity['mobcent']['leftNums'];
        // if ($post['invisible'] == 0) {
            if ($applied && $isVerified < 2) {
                if (!$isVerified) {
                    $action['description'] = WebUtils::t('您的加入申请已发出，请等待发起者审批');
                } else {
                    $action['description'] = WebUtils::t('您已经参加了此活动');
                }
                if (!$isActivityClose) {
                    $action['title'] = WebUtils::t('取消报名');
                    $action['type'] = DzForumThread::TYPE_ACTIVITY_ACTION_CANCEL;
                    $action['info'] = self::_getActivityActionCancelInfo($activity);
                }
            } elseif (!$isActivityClose) {
                if ($isVerified != 2) {
                    if (!$activity['number'] || $leftNums > 0) {
                        global $_G;
                        if($_G['uid']) { 
                            $action['type'] = DzForumThread::TYPE_ACTIVITY_ACTION_APPLY;
                            $action['info'] = self::_getActivityActionApplyInfo($activity);
                        } else { 
                            $action['type'] = DzForumThread::TYPE_ACTIVITY_ACTION_LOGIN;
                            $action['info'] = null;
                        } 
                        $action['title'] = WebUtils::t('我要参加');
                    }
                } else {
                    $action['type'] = DzForumThread::TYPE_ACTIVITY_ACTION_APPLY;
                    $action['info'] = self::_getActivityActionApplyInfo($activity, true);
                    $action['title'] = WebUtils::t('完善资料');
                }
                $action['description'] = '';
            }
            if ($isActivityClose) {
                $action['title'] = WebUtils::t('活动已结束');
                $action['type'] = DzForumThread::TYPE_ACTIVITY_ACTION_NONE;
                $action['info'] = null;
            }
        // }
        return $action;
    }

    private static function _getActivityActionApplyInfo($activity, $improve=false) {
        $applyInfo = array('title' => '', 'description' => '', 'options' => array());
        
        // title
        $applyInfo['title'] = $improve ? WebUtils::t('完善资料') : WebUtils::t('我要参加');
        // description
        global $_G;
        if ($_G['setting']['activitycredit'] && $activity['credit'] && !$activity['mobcent']['applied']) {
            $applyInfo['description'] = sprintf(WebUtils::t('注意：参加此活动将扣除您 %s %s'), 
                $activity['credit'], 
                $_G['setting']['extcredits'][$_G['setting']['activitycredit']]['title']
            );
        }
        // options
        if ($activity['cost']) {
            $applyInfo['options'][] = self::_makeInputElement('radio', 'payment', 0, WebUtils::t('支付方式'), null, array(
                self::_makeInputElement('radioOption', 'payment', 0, WebUtils::t('承担自己应付的花销')),
                self::_makeInputElement('radioOption', 'payment', 1, WebUtils::t('支付'), null, array(
                    self::_makeInputElement('text', 'payvalue', '', WebUtils::t('元'))
                )),
            ));
        }
        $userFields = !empty($activity['ufield']['userfield']) ? $activity['ufield']['userfield'] : array();
        if (!empty($userFields)) { 
            foreach($userFields as $field) {
                $option = $activity['mobcent']['options'][$field];
                if ($option['available']) {
                    $userFieldData = unserialize($activity['mobcent']['applyInfo']['ufielddata']);
                    $value = !empty($userFieldData['userfield'][$field]) ? $userFieldData['userfield'][$field] : '';
                    if ($option['formtype'] == 'select') {
                        switch ($option['fieldid']) {
                            case 'gender':
                                $applyInfo['options'][] = self::_makeInputElement(
                                    $option['formtype'], $option['fieldid'],
                                    $value, $option['title'], array('required' => 1), array(
                                        self::_makeInputElement('selectOption', $option['fieldid'], 0, WebUtils::t('保密')),
                                        self::_makeInputElement('selectOption', $option['fieldid'], 1, WebUtils::t('男')),
                                        self::_makeInputElement('selectOption', $option['fieldid'], 2, WebUtils::t('女')),
                                    )
                                );
                                break;
                            case 'birthday':
                                // 年
                                $year = (int)date('Y');
                                $yearElements[] = self::_makeInputElement('selectOption', 'birthyear', '', WebUtils::t('年'));
                                for ($i = $year; $i > $year-100; $i--) {
                                    $yearElements[] = self::_makeInputElement('selectOption', 'birthyear', $i, $i);
                                }
                                $applyInfo['options'][] = self::_makeInputElement(
                                    $option['formtype'], 'birthyear',
                                    !empty($userFieldData['userfield']['birthyear']) ? $userFieldData['userfield']['birthyear'] : '', 
                                    $option['title'], array('required' => 1), $yearElements
                                );
                                // 月
                                $monthElements[] = self::_makeInputElement('selectOption', 'birthmonth', '', WebUtils::t('月'));
                                for ($i = 1; $i <= 12; $i++) {
                                    $monthElements[] = self::_makeInputElement('selectOption', 'birthmonth', $i, $i);
                                }
                                $applyInfo['options'][] = self::_makeInputElement(
                                    $option['formtype'], 'birthmonth',
                                    !empty($userFieldData['userfield']['birthmonth']) ? $userFieldData['userfield']['birthmonth'] : '', 
                                    '', array('required' => 0), $monthElements
                                );
                                // 日
                                $dayElements[] = self::_makeInputElement('selectOption', 'birthday', '', WebUtils::t('日'));
                                for ($i = 1; $i <= 31; $i++) {
                                    $dayElements[] = self::_makeInputElement('selectOption', 'birthday', $i, $i);
                                }
                                $applyInfo['options'][] = self::_makeInputElement(
                                    $option['formtype'], 'birthday',
                                    !empty($userFieldData['userfield']['birthday']) ? $userFieldData['userfield']['birthday'] : '', 
                                    '', array('required' => 0), $dayElements
                                );
                                break;
                            case 'birthcity':
                            case 'residecity':
                                $resideValue = '';
                                $key = rtrim($option['fieldid'], 'city');
                                if (!empty($userFieldData['userfield']["{$key}province"]) && 
                                    !empty($userFieldData['userfield']["{$key}city"]) &&
                                    !empty($userFieldData['userfield']["{$key}dist"])) {
                                    $resideValue = $userFieldData['userfield']["{$key}province"] . ' '.
                                        $userFieldData['userfield']["{$key}city"] . ' ' .
                                        $userFieldData['userfield']["{$key}dist"] . ' ' .
                                        (!empty($userFieldData['userfield']["{$key}community"]) ?
                                            $userFieldData['userfield']["{$key}community"] : '');
                                }
                                $applyInfo['options'][] = self::_makeInputElement(
                                    $option['formtype'], $option['fieldid'],
                                    $resideValue, $option['title'], array('required' => 1)
                                );
                                break;                      
                            default:
                                $selectOptions = explode("\n", $option['choices']);
                                $elements = array();
                                foreach ($selectOptions as $selectOption) {
                                    $elements[] = self::_makeInputElement('selectOption', $option['fieldid'], $selectOption, $selectOption);
                                }
                                $applyInfo['options'][] = self::_makeInputElement(
                                    $option['formtype'], $option['fieldid'],
                                    $value, $option['title'], array('required' => 1), $elements
                                );
                                break;
                        }
                    } else {
                        $applyInfo['options'][] = self::_makeInputElement(
                            $option['formtype'], $option['fieldid'],
                            $value, $option['title'], array('required' => 1)
                        );
                    }
                }
            }
        }
        $message = !empty($activity['mobcent']['applyInfo']['message']) ? $activity['mobcent']['applyInfo']['message'] : '';
        $applyInfo['options'][] = self::_makeInputElement('textarea', 'message', $message, WebUtils::t('留言'));

        return $applyInfo;
    }

    private static function _getActivityActionCancelInfo($activity) {
        $cancelInfo = array(
            'title' => WebUtils::t('取消报名'), 
            'description' => '', 
            'options' => array(self::_makeInputElement('text', 'message', '', WebUtils::t('留言'))),
        );
        return $cancelInfo;
    }

    private static function _getActivityApplyList($activity) {
        return self::_getActivityApplyListHelper($activity, false);
    }

    private static function _getActivityApplyListVerified($activity) {
        return self::_getActivityApplyListHelper($activity, true);
    }

    private static function _getActivityApplyListHelper($activity, $verified) {
        $list = null;
        $activityList = $verified ? $activity['mobcent']['applyListVerified'] : $activity['mobcent']['applyList'];
        if (!empty($activityList)) {
            $list['title'] =  $verified ? 
                WebUtils::t('暂未通过 (' . $activity['mobcent']['noVerifiedNums'] . ' 人)') :
                WebUtils::t('已通过 (' . $activity['mobcent']['applyNums'] . ' 人)');
            $list['summary'] = '';
            $list['content'] = array();
            // $list['summary'] .= sprintf(WebUtils::t("\t\t留言\t%s申请时间\n"), $activity['cost'] ? WebUtils::t("每人花销\t") : '');    
            $list['summary'] .= sprintf(WebUtils::t("\t\t申请时间\n"));    
            global $_G;
            foreach ($activityList as $member) {
                $list['summary'] .= sprintf("%s\t%s\t%s\t%s\n", 
                    $member['username'],
                    // 目前先去掉多余的活动帖信息
                    '', // $_G['forum_thread']['authorid'] == $_G['uid'] && $member['message'] ? WebUtils::emptyHtml($member['message']) : '',
                    '', // $activity['cost'] ? ($member['payment'] >= 0 ? WebUtils::t("{$member['payment']} 元"): WebUtils::t('自付')) : '',
                    WebUtils::emptyHtml($member['dateline'])
                );
            }    
        }
        return $list;
    }

    private static function _makeInputElement($type, $name, $value='', $label='', $attributes=null, $elements=array()) {
        return array('type' => $type, 'name' => $name, 'value' => $value,
            'label' => $label, 'attributes' => $attributes, 'elements' => $elements, 
        );
    }
}