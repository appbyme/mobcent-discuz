<?php

/**
 * 帖子model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// post表status字段的自定义公式
// define('STATUS_APPBYME', 0x1000000);
// define('STATUS_SEND_FROM_APP_ANDROID', STATUS_APPBYME|STATUS_APPBYME>>1);
// define('STATUS_SEND_FROM_APP_APPLE', STATUS_APPBYME|STATUS_APPBYME>>2);

class DzForumPost extends DiscuzAR {

    const STATUS_SEND_FROM_APP_ANDROID = 25165824;
    const STATUS_SEND_FROM_APP_APPLE = 20971520;
    
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{forum_post}}';
    }

    public function rules() {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    public static function getTableName($tid) {
        $dzVersion = MobcentDiscuz::getMobcentDiscuzVersion();
        $tableName = '';
        if ($dzVersion == 'x20') {
            $tableName = 'forum_post';
        } else {
            $tableName = C::t('forum_post')->get_tablename('tid:'.$tid);
        }
        return $tableName;
    }

    // 获取回帖信息
    public static function getPostByTidAndPid($tid, $pid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE pid=%d
        ',
            array(self::getTableName($tid), $pid)
        );
    }

    // 获取主题帖信息
    public static function getFirstPostByTid($tid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE tid=%d AND first=%d
        ',
            array(self::getTableName($tid), $tid, 1)
        );
    }


    public static function getPostsByTid($tid, $page=1, $pageSize=10, $params=array()) {
        $authorSql = isset($params['authorId']) && $params['authorId'] != 0 ? 'AND authorid=' . $params['authorId'] : '';
        $sql = sprintf('
            SELECT *
            FROM %%t
            WHERE tid=%%d AND first!=%%d AND invisible>=%%d %s
            ORDER BY %s
            LIMIT %%d, %%d
            ',
            $authorSql, !empty($params['order']) ? $params['order'] : 'dateline ASC'
        );
        return DbUtils::getDzDbUtils(true)->queryAll($sql, array(
            self::getTableName($tid), $tid, 1, 0,
            $pageSize * ($page-1), $pageSize
        ));
    }
    
    public static function getCountByTid($tid, $params) {
        $authorSql = isset($params['authorId']) && $params['authorId'] != 0 ? 'AND authorid=' . $params['authorId'] : '';
        $sql = '
            SELECT COUNT(*)
            FROM %t
            WHERE tid=%d AND first!=%d AND invisible>=%d ' . $authorSql;
        return (int)DbUtils::getDzDbUtils(true)->queryScalar($sql, array(
            self::getTableName($tid), $tid, 1, 0
        ));
    }

    public static function isAnonymous($tid, $pid) {
        $postInfo = self::_getPostInfo($tid, $pid);
        return !empty($postInfo) && ($postInfo['anonymous'] == 1);
    }

    public static function transPostContentToHtml($post) {
        Mobcent::import(sprintf(
            '%s/forum_viewthread_%s.php', 
            MOBCENT_APP_ROOT . '/components/discuz/forum', 
            MobcentDiscuz::getMobcentDiscuzVersion()
        ));

        ForumUtils::initForum($post['fid'], $post['tid']);

        loadcache('usergroups');
        $userInfo = UserUtils::getUserInfo($post['authorid']);
        $post = array_merge($userInfo, $post);

        global $_G;
        
        // 处理主题价格
        $_G['forum_threadpay'] = FALSE;
        if ($post['first']) {
            if($_G['forum_thread']['price'] > 0 && $_G['forum_thread']['special'] == 0) {
                if($_G['setting']['maxchargespan'] && TIMESTAMP - $_G['forum_thread']['dateline'] >= $_G['setting']['maxchargespan'] * 3600) {
                    C::t('forum_thread')->update($_G['tid'], array('price' => 0), false, false, $archiveid);
                    $_G['forum_thread']['price'] = 0;
                } else {
                    $exemptvalue = $_G['forum']['ismoderator'] ? 128 : 16;
                    if(!($_G['group']['exempt'] & $exemptvalue) && $_G['forum_thread']['authorid'] != $_G['uid']) {
                        if(!(C::t('common_credit_log')->count_by_uid_operation_relatedid($_G['uid'], 'BTC', $_G['tid']))) {
                            require_once libfile('thread/pay', 'include');
                            $_G['forum_threadpay'] = TRUE;
                        }
                    }
                }
            }   
        }
        
        $lastvisit = $_G['member']['lastvisit'];
        $ordertype = $maxposition = 0;
        
        // 处理附件
        $_G['forum_attachpids'] = $_G['forum_attachtags'] = '';
        $_G['tid'] = $post['tid'];

        // 去掉干扰码
        $_G['forum']['jammer'] = 0;

        $post = viewthread_procpost($post, $lastvisit, $ordertype, $maxposition);

        $postlist[$post['pid']] = $post;
        if ($_G['forum_attachpids'] && !defined('IN_ARCHIVER')) {
            require_once libfile('function/attachment');
            if (is_array($threadsortshow) && !empty($threadsortshow['sortaids'])) {
                $skipaids = $threadsortshow['sortaids'];
            }
            parseattach($_G['forum_attachpids'], $_G['forum_attachtags'], $postlist, $skipaids);
        }
        if (empty($postlist)) {
            showmessage('post_not_found');
        } elseif (!defined('IN_MOBILE_API')) {
            foreach($postlist as $pid => $post) {
                // 取出没有插入的附件
                if (!empty($post['imagelist']))
                    $postlist[$pid]['message'] .= showattach($post, 1);
                
                $postlist[$pid]['message'] = preg_replace("/\[attach\]\d+\[\/attach\]/i", '', $postlist[$pid]['message']);
            }
        }

        if ($post['first'] && $_G['forum_threadpay']) {
            $postlist[$pid]['message'] = $thread['freemessage'];
        }

        return $postlist[$post['pid']];
    }

    public static function _getPostInfo($tid, $pid) {
        return is_numeric($pid) ? self::getPostByTidAndPid($tid) : $pid;
    }
}