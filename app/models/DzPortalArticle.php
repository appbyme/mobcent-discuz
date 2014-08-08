<?php

/**
 * 门户文章 model类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzPortalArticle extends DiscuzAR {

    const ARTICLE_STATUS = 0; // 文章已经审核

    // 获取文章信息
    public function getArticleByAid($aid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE aid=%d
            LIMIT 1
            ',
            array('portal_article_title', $aid)
        );
    }

    // 查询复合条件的数目
    public static function getByCatidCount($catids, $params=array()) {

        $sql .= 'SELECT COUNT(*)
                 FROM %t
                 WHERE catid IN(%n)
                 AND status=%d
                 ';

        $term = array('portal_article_title', $catids, self::ARTICLE_STATUS);

        if ($params['article_picrequired'] > 0) {
            $sql .= ' AND pic !=%s';
            $term[] = '';
        }

        if ($params['article_starttime']> 0) {
            $sql .= ' AND dateline >=%d';
            $term[] = $params['article_starttime'];
        }

        if ($params['article_endtime'] > 0) {
            $sql .= ' AND dateline <=%d';
            $term[] = $params['article_endtime'];            
        }
        
        if ($params['article_publishdateline'] > 0) {
            $time = time() - $params['article_publishdateline'];
            $sql .= ' AND dateline >%d';
            $term[] = $time;          
        }
        return DbUtils::getDzDbUtils(true)->queryScalar($sql, $term);        
    }

    // 通过catid和过滤条件来查询符合条件的的信息
    public static function getByCatidData($catids, $offset, $limit, $params=array()) {
        $sql = 'SELECT at.*, ac.viewnum, ac.commentnum, ac.favtimes, ac.sharetimes
                FROM %t as at INNER JOIN %t as ac
                ON at.aid = ac.aid 
                WHERE at.catid IN (%n)
                AND status=%d
                ';
        $term = array('portal_article_title', 'portal_article_count', $catids, self::ARTICLE_STATUS);

        if ($params['article_picrequired'] > 0) {
            $sql .= ' AND pic !=%s';
            $term[] = '';
        }     

        if ($params['article_starttime']> 0) {
            $sql .= ' AND dateline >=%d';
            $term[] = $params['article_starttime'];
        }

        if ($params['article_endtime'] > 0) {
            $sql .= ' AND dateline <=%d';
            $term[] = $params['article_endtime'];            
        }

        if ($params['article_publishdateline'] > 0) {
            $time = time() - $params['article_publishdateline'];
            $sql .= ' AND dateline >%d';
            $term[] = $time;          
        }

        if (isset($params['article_orderby'])) {
            $sql .= ' ORDER BY ' . $params['article_orderby'] . ' DESC';
        } else {
            $sql .= ' ORDER BY dateline DESC';
        }
        
        $sql .= ' LIMIT %d, %d';
        $term[] = $offset;
        $term[] = $limit;
        return DbUtils::getDzDbUtils(true)->queryAll($sql, $term);        
    }

    public static function getArticleCountByAid($aid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE aid=%d
            ',
            array('portal_article_count', $aid)
        );         
    }
}