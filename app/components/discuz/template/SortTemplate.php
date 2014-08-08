<?php 

/**
 * 分类信息 模板
 * 
 * 此文件为安米默认的模板，请不要修改!!!
 * 如果你想按照你的需求修改模板，请复制一份这个文件到相同目录，并且命名为my_SortTemplate.php.
 * 
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SortTemplate {

    public function getTopicSort($threadsortshow) {
        $sort = array('title' => '', 'summary' => '');

        if ($threadsortshow['optionlist']) {
            if($threadsortshow['optionlist'] == 'expire') {
                $sort['summary'] = WebUtils::t("该信息已经过期\n");
            } else {
                global $_G;
                $sort['title'] = $_G['forum']['threadsorts']['types'][$_G['forum_thread']['sortid']];
                if (is_array($threadsortshow['optionlist'])) {
                    foreach($threadsortshow['optionlist'] as $option) { 
                        if($option['type'] != 'info') {
                            $sort['summary'] .= sprintf("%s :\t", $option['title']);
                            if ($option['value'] || ($option['type'] == 'number' && $option['value'] !== '')) { 
                                $option['value'] = WebUtils::emptyHtml($option['value']);
                                $sort['summary'] .= $option['value'] . $option['unit'];
                            }
                            $sort['summary'] .= "\n";
                        }
                    }
                }
            }
        }

        return $sort;
    }
}