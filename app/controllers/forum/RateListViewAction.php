<?php

/**
 * 显示全部评分接口
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) 
{
    exit('Access Denied');
}
// Mobcent::setErrors();
class RateListViewAction extends MobcentAction
{
    public function run($tid, $pid) 
    {
        $res = ForumUtils::rateList($tid, $pid);
        $this->getController()->renderPartial(
            'rateListView',
            array(
                'loglist' => $res['loglist'],
                'logcount' => $res['logcount']
            )
        );
    }
}