<?php

/**
 * 板块分类信息
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class ClassificationAction extends MobcentAction 
{
    public function run($sortid)
    {
        $res = $this->initWebApiArray();

        $res = $this->_getClassification($res, $sortid);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getClassification($res, $sortid) {
        $typevarlist = array();
        $list = array();

        $threadtypearr = C::t('forum_threadtype')->fetch($sortid);
        $sortoptions = $jsoptionids = '';
        $showoption = array();
        $typevararr = C::t('forum_typevar')->fetch_all_by_sortid($sortid, 'ASC');
        $typeoptionarr = DzClassifiedBysortid::getTypeoptionInfo($sortid);
        if($threadtypearr['expiration']) {
            $res['body']['classified'] = $this->_getTimeLimit();
        }

        foreach($typevararr as $tpvs) {
            $typevar[] = $tpvs;
        }

        foreach($typeoptionarr as $k=>$v) {
            $tempTypeoptionarr[] = array_merge($v, $typevar[$k]);
        }

        foreach($typevar as $trs) {
            unset($trs[sortid]);
            unset($trs[available]);
            unset($trs[search]);
            $tempTrs[] = $trs;
        }

        for($sx=0; $sx <count($tempTypeoptionarr); $sx++) {
            if(intval($tempTypeoptionarr[$sx][available]) == 0) {
                unset($tempTypeoptionarr[$sx]);
            }
        }

        $classifiedArr = array();
        foreach($tempTypeoptionarr as $csTest) {
            unset($csTest[sortid]);
            unset($csTest[optionid]);
            unset($csTest[available]);
            unset($csTest[search]);
            unset($csTest[displayorder]);
            unset($csTest[subjectshow]);
            $classifiedArr[] = $csTest;
        }

        $classifList = array();
        for($i=0; $i <count($classifiedArr); $i++) {
            $tps = $classifiedArr[$i][classifiedType];
            $classifiedArr[$i][classifiedRules] = unserialize($classifiedArr[$i][classifiedRules]);

            if(isset($classifiedArr[$i][classifiedRules][maxlength]) && is_numeric(($classifiedArr[$i][classifiedRules][maxlength]))) {
                $classifiedArr[$i][classifiedRules][maxlength] = intval((int)$classifiedArr[$i][classifiedRules][maxlength]/3);
            }

            if($tps == "calendar") {
                $classifiedArr[$i][classifiedRules][defaultvalue] = date("Y-m-d",time());
                $classifiedArr[$i][classifiedRules][isdate] = 1;
            }

            if($tps == "number" || $tps == "range") {
                $classifiedArr[$i][classifiedRules][isnumber] = 1;
            }

            if($tps == "calendar" || $tps == "email" || $tps == "url" || $tps == "number" || $tps=="range") {
                $classifiedArr[$i][classifiedType] = "text";
            }

            switch($classifiedArr[$i][classifiedType]) {
                case "text":
                    $classifiedArr[$i][classifiedType] = 1;
                    break;
                case "radio":
                    $classifiedArr[$i][classifiedType] = 2;
                    break;
                case "checkbox":
                    $classifiedArr[$i][classifiedType] = 3;
                    break;
                case "select":
                    $classifiedArr[$i][classifiedType] = 4;
                    break;
                case "textarea":
                    $classifiedArr[$i][classifiedType] = 5;
                    break;
                case "image":
                    $classifiedArr[$i][classifiedType] = 6;
                    break;
                default:
                    $classifiedArr[$i][classifiedType] = 0;
                    break;
            }

            $choiceArr = explode("\r\n",$classifiedArr[$i][classifiedRules][choices]);
            if($classifiedArr[$i][classifiedRules][choices] != "") {
                foreach($choiceArr as $charr){
                    $choiceVal = explode("=" , $charr);
                    if($choiceVal[0] == intval($choiceVal[0])){
                        $tempchoiceVal['name'] = $choiceVal[1];
                        $tempchoiceVal['value'] = $choiceVal[0];
                        $cd[] = $tempchoiceVal;
                        foreach($classifList as $classifL){
                            $msd = count($classifL);
                        }
                    }
                }
            $classifiedArr[$i][classifiedRules][choices] = array_slice($cd, $msd);
            $classifList[] = $cd;
        }

            if($classifiedArr[$i][classifiedRules] == false){
                $classifiedArr[$i][classifiedRules] = array();
            }
        }

        $res['body']['classified'] = $classifiedArr;
        return $res;   
    }

    private function _getTimeLimit() {
        $timeArray = array(
            "classifiedTopId" =>"1",
            "classifiedTitle" => WebUtils::t('信息有效期'),
            "classifiedName" => "typeexpiration",
            "classifiedType" => 4,
            "classifiedRules" => array(
                "choices" => array(
                    array(
                        'name' => WebUtils::t('3天'),
                        'value' => '259200'
                    ),
                    array(
                        'name' => WebUtils::t('5天'),
                        'value' => '432000'
                    ),
                    array(
                        'name' => WebUtils::t('7天'),
                        'value' => '604800'
                    ),
                    array(
                        'name' => WebUtils::t('1个月'),
                        'value' => '2592000'
                    ),
                    array(
                        'name' => WebUtils::t('3个月'),
                        'value' => '7776000'
                    ),
                    array(
                        'name' => WebUtils::t('半年'),
                        'value' => '15552000'
                    ),
                    array(
                        'name' => WebUtils::t('1年'),
                        'value' => '31536000'
                    ),
                ),
                "inputsize" => ""
            ),
            "required" => "1",
            "unchangeable" => "0"
        );
        return $timeArray;
    }
}

class DzClassifiedBysortid extends DiscuzAR {
    public static function getTypeoptionInfo($sortid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT a.optionid,a.classid as classifiedTopId,a.title as classifiedTitle,a.identifier as classifiedName,a.type as classifiedType,a.rules as classifiedRules,b.* 
            FROM %t as a INNER JOIN %t as b 
            ON a.optionid = b.optionid 
            WHERE b.sortid = %d 
            ORDER BY b.displayorder ASC
            ',
            array('forum_typeoption', 'forum_typevar', $sortid)
        );
    }
}
?>
