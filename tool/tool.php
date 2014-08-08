<?php
require_once '../../config/config_ucenter.php';
require_once '../Config/public.php';
function config(){
    if($_GET['test']==1){
        $urlencode = 0;
    }else{
        $urlencode = 1;
    }
    return $config = array(
            "urlencode" =>  $urlencode,
            "ob_gzip"   =>  1,
    );
}

function ob_gzip($content){
    if(!headers_sent()&&extension_loaded("zlib")&&strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip")){
        $content = gzencode($content,9);
        header("Content-Encoding: gzip");
        header("Vary: Accept-Encoding");
        header("Content-Length: ".strlen($content));
    }
    return $content;
}

function echo_json($data,$encode = UC_DBCHARSET){

    if($encode == 'utf8')
    {
        $jsons = json_encode($data);
    }
    else
    {
        $config = config();
        header('Content-type: text/html; charset='.$encode);
        $data = array_utf8($data);
        $jsons = json_encode($data);
        $jsons = str_replace('\\\\u', '\\u', $jsons);
        if($config['urlencode']) $jsons = $jsons;
    }


    return $jsons;
}
function echo_mysql_json($data,$encode = UC_DBCHARSET){

    if($encode == 'utf8')
    {
        $jsons = json_encode($data);
    }
    else
    {
        $config = config();
        header('Content-type: text/html; charset='.$encode);
        $data = array_utf8($data);
        $jsons = json_encode($data);
        $jsons = str_replace('\\\\u', '\\u', $jsons);
        if($config['urlencode']) $jsons = $jsons;
    }


    return $jsons;
}


function echo_array($json,$encode = UC_DBCHARSET){
    if($encode == 'utf8')
    {

        $array = json_decode($json);
        $array = array_gbk($array,$input_encoding='utf-8',$output_encoding='utf-8');
    }
    else
    {
        $array = json_decode($json,true);
        $array = array_gbk($array,$input_encoding='UTF-8',$output_encoding='GBK');
    }

    return $array;
}
function echo_urlencode($str,$encode = UC_DBCHARSET)
{
    if($encode == 'utf8')
    {

        $array = urlencode($str);
    }
    else
    {

        $array = urlencode($str);
        $array = mb_convert_encoding($array, 'GBK' , 'UTF-8');
    }
    return $array;
}

function echo_urldecode($str,$encode = UC_DBCHARSET)
{
    if($encode == 'utf8')
    {

        $array = urldecode($str);
    }
    else
    {

        $array = urldecode($str);
        $array = mb_convert_encoding($array, 'GBK' , 'UTF-8');
    }
    return $array;
}

function decodehtml($html){
    return str_replace('<br \/>', '\r\n', $html);

}
function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_REAL_FORWARDED_FOR']) &&
            preg_match('/^([0-9]{1,3}\.){3}
                  [0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_FORWARDED_FOR'])) {
                  $ip = $_SERVER['HTTP_X_REAL_FORWARDED_FOR'];
    }
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
            preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}
                $/', $_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    return $ip;
}


function array_utf8($data_array,$input_encoding='gb2312',$output_encoding='utf-8'){
    foreach ($data_array as $key=>$val)
    {
        $key = iconv($input_encoding,$output_encoding,$key);
        if(is_array($val)){
            if(empty($val)){
                $data[$key]=array();
                continue;
            }

            $val=array_utf8($val,$input_encoding,$output_encoding);
        }elseif(is_int($val)){
            $val = (int)iconv($input_encoding,$output_encoding,$val);
        }elseif(is_bool($val)){
            $val = (bool)iconv($input_encoding,$output_encoding,$val);
        }else{
            $val = unicode_encode($val);
        }
            $data[$key]=$val;
    }
    return $data;
}

function unicode_encode($name,$encode = UC_DBCHARSET)
{
    $encode =$encode=='utf8'?'utf-8':'gbk';
    $system = Common::getInstance();
    $unicode = $system -> platform();
    $name = iconv($encode, "$unicode//IGNORE", $name);
    $len = strlen($name);
    $str = '';
    for ($i = 0; $i < $len - 1; $i = $i + 2)
    {
        $c = $name[$i];
        $c2 = $name[$i + 1];

        if (ord($c) > 0)
        {
            $part1 = base_convert(ord($c), 10, 16);
            if(strlen($part1) == 1)
                $part1 = "0" . $part1;

            $part2 = base_convert(ord($c2), 10, 16);
            if(strlen($part2) == 1)
                $part2 = "0" . $part2;

            $str .= '\u'. $part1 . $part2;
        }
        else
        {
            if(ord($c) == 0){
                $part2 = base_convert(ord($c2), 10, 16);
                if(strlen($part2) == 1)
                    $part2 = "0" . $part2;

                $str .= '\u00'. $part2;
            } else {
                $str .= $c2;
            }
        }
    }
    return $str;
}

function array_gbk($data_array,$input_encoding='UTF-8',$output_encoding='GBK'){

    foreach ($data_array as $key=>$val)
    {
        $key = iconv($input_encoding,$output_encoding,$key);
        if(!is_string($val) && !is_int($val)){
            $val=array_gbk($val,$input_encoding,$output_encoding);
        }else{
            $val = mb_convert_encoding($val, $output_encoding,$input_encoding);
        }
        $data[$key]=$val;

    }
    return $data;
}
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

function forum_2(&$forum) {
    global $_G;
    $lastvisit = $_G['member']['lastvisit'];
    if(!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || !empty($forum['allowview']) || (isset($forum['users']) && strstr($forum['users'], "\t$_G[uid]\t"))) {
        $forum['permission'] = 2;
    } elseif(!$_G['setting']['hideprivate']) {
        $forum['permission'] = 1;
    } else {
        return FALSE;
    }

    if($forum['icon']) {
        $forum['icon'] = get_forumimg($forum['icon']);
        $forum['icon'] = '<a href="forum.php?mod=forumdisplay&fid='.$forum['fid'].'"><img src="'.$forum['icon'].'" align="left" alt="" /></a>';
    }

    $lastpost = array(0, 0, '', '');

    $forum['lastpost'] = is_string($forum['lastpost']) ? explode("\t", $forum['lastpost']) : $forum['lastpost'];

    $forum['lastpost'] =count($forum['lastpost']) != 4 ? $lastpost : $forum['lastpost'];

    list($lastpost['tid'], $lastpost['subject'], $lastpost['dateline'], $lastpost['author']) = $forum['lastpost'];

    $forum['folder'] = (isset($_G['cookie']['fid'.$forum['fid']]) && $_G['cookie']['fid'.$forum['fid']] > $lastvisit ? $_G['cookie']['fid'.$forum['fid']] : $lastvisit) < $lastpost['dateline'] ? ' class="new"' : '';

    if($lastpost['tid']) {
        /*$lastpost['dateline'] = dgmdate($lastpost['dateline'], 'u');*/
        $lastpost['authorusername'] = $lastpost['author'];
        if($lastpost['author']) {
            /*$lastpost['author'] = '<a href="home.php?mod=space&username='.rawurlencode($lastpost['author']).'">'.$lastpost['author'].'</a>';*/
        }
        $forum['lastpost'] = $lastpost;
    } else {
        $forum['lastpost'] = $lastpost['authorusername'] = '';
    }

    $forum['moderators'] = moddisplay($forum['moderators'], $_G['setting']['moddisplay'], !empty($forum['inheritedmod']));

    if(isset($forum['subforums'])) {
        $forum['subforums'] = implode(', ', $forum['subforums']);
    }

    return TRUE;
}

function mkfeed_2($feed, $actors=array()) {
    global $_G;
    $feed['title_data'] = empty($feed['title_data'])?array():(is_array($feed['title_data'])?$feed['title_data']:@dunserialize($feed['title_data']));
    if(!is_array($feed['title_data'])) $feed['title_data'] = array();

    $feed['body_data'] = empty($feed['body_data'])?array():(is_array($feed['body_data'])?$feed['body_data']:@dunserialize($feed['body_data']));
    if(!is_array($feed['body_data'])) $feed['body_data'] = array();
    $searchs = $replaces = array();
    if($feed['title_data']) {
        foreach (array_keys($feed['title_data']) as $key) {
            $searchs[] = '{'.$key.'}';
            $replaces[] = $feed['title_data'][$key];
        }
    }

    $searchs[] = '{actor}';
    $replaces[] = empty($actors)?"$feed[username]":implode(lang('core', 'dot'), $actors);
    $feed['title_template'] = str_replace($searchs, $replaces, $feed['title_template']);
    $feed['title_template'] = feed_mktarget_2($feed['title_template']);

    $searchs = $replaces = array();
    $searchs[] = '{actor}';
    $replaces[] = empty($actors)?"$feed[username]":implode(lang('core', 'dot'), $actors);
    if($feed['body_data'] && is_array($feed['body_data'])) {
        foreach (array_keys($feed['body_data']) as $key) {
            $searchs[] = '{'.$key.'}';
            $replaces[] = $feed['body_data'][$key];
        }
    }

    $feed['magic_class'] = '';
    if(!empty($feed['body_data']['magic_thunder'])) {
        $feed['magic_class'] = 'magicthunder';
    }

    $feed['body_template'] = str_replace($searchs, $replaces, $feed['body_template']);
    $feed['body_template'] = feed_mktarget_2($feed['body_template']);
    $feed['body_general'] = feed_mktarget_2($feed['body_general']);

    if(is_numeric($feed['icon'])) {
        $feed['icon_image'] = "http://appicon.manyou.com/icons/{$feed['icon']}";
    } else {
        $feed['icon_image'] = STATICURL."image/feed/{$feed['icon']}.gif";
    }

    $feed['new'] = 0;
    if($_G['cookie']['home_readfeed'] && $feed['dateline']+300 > $_G['cookie']['home_readfeed']) {
        $feed['new'] = 1;
    }

    return $feed;
}

function feed_mktarget_2($html) {
    global $_G;

    if($html && $_G['setting']['feedtargetblank']) {
        $html = preg_replace("/target\=([\'\"]?)[\w]+([\'\"]?)/i", '', $html);
        $html = preg_replace("/<a.*>(.*)<\/a>/i", '\\1', $html);
    }
    return $html;
}
function action_log($data) {
    @$fp2 = fopen ( "log.txt", 'a+' );
    fwrite ( $fp2, "[" . date ( 'Y-m-d H:i:s' ) . "]:" . $data . "\r\n" );
    fclose ( $fp2 );
}
function is_gif($path)
{
    $arr_path = explode('/',$path);
    if(in_array('mobcent', $arr_path))
    {
        if(substr($path, -4,4) == '.gif')
        {
            $info = str_replace('/mobcent/data/attachment/forum/mobcentSmallPreview/', '/data/attachment/forum/', $path);
        }else {
            $info = str_replace('/mobcent/data/attachment/forum/mobcentSmallPreview/', '/mobcent/data/attachment/forum/mobcentBigPreview/', $path);
        }

    }else {
        $info =$path;
    }
    return $info;
}
/*rx 20130827 xiugai wang luo tu pian de wen ti*/
function get_content_img($content,$attachmentAId){
    preg_match_all("/\<img.*?src\=\"(.*?)\"[^>]*>/i", $content, $imgs);
    foreach($imgs[1] as $key=>$value){
        $path = is_gif($imgs['1'][$key].$imgs['2'][$key]);
        $data['data'][]=array(
                'infor'=>$imgs['1'][$key].$imgs['2'][$key],
                'originalInfo' =>$path,
                'aid'=>(int)$attachmentAId[$key],
                'type' =>1);
    }
    $content = preg_replace('#<img[^>]*/>#i', '', $content);
    $data['content'] = $content;
    return $data;
}
/*end rx 20130827*/
function text_replace($str){
    $str = htmlspecialchars_decode($str);
    $str = str_replace('</h>','</h><br />',$str);
    $str = str_replace('</p>','</p><br />',$str);
    $str = str_replace('</font>','</font><br />',$str);
    $str = str_replace('</strong>', '</strong><br />', $str);
    $str = str_replace('<br />', "\r\n", $str);
    $str = str_replace('<br/>', "\r\n", $str);
    $str = str_replace('<br>', "\r\n", $str);
    $str = str_replace('<BR>', "\r\n", $str);
    $str = str_replace('</div>', "\r\n", $str);
    $str = str_replace('<strong>', '', $str);
    $str = str_replace('</strong>', '', $str);
    $str = str_replace('<i>', '', $str);
    $str = str_replace('</i>', '', $str);
    $str = str_replace('<u>', '', $str);
    $str = str_replace('<ul>', '', $str);
    $str = str_replace('</ul>', '', $str);
    $str = str_replace('<li>', '', $str);
    $str = str_replace('</li>', '', $str);
    $str = str_replace('</u>', '', $str);
    $str = str_replace('&nbsp;',' ', $str);
    $str = str_replace('\\"', '"', $str);
    $str = str_replace('&lt;', '<', $str);
    $str = str_replace('&quot;', '"', $str);
    $str = str_replace('&gt;', '>', $str);
    $str = str_replace('<i class="pstatus">', '', $str);
    $str = str_replace('<hr class="l">', '', $str);
    return $str;
}
function content_a($content){
    $res=preg_match_all('#<a[^>]*href=(?:\"|\'){0,1}([^ ]*)(?:\"|\'){0,1}[^>]*>([^>]+)<\/a>#i', $content,$as);
    $content = str_replace($as, '', $content);
    $data['content'] = $content;
    foreach($as['0'] as $key=>$value){
        $content = str_replace($value, '', $content);
        $data['data'][]=array(
                'url'  =>str_replace('"', '', $as['1'][$key]),
                'infor'=>$as['2'][$key],
                'type' =>0);
    }
    $data['content'] = $content;
    return $data;
}
function del_symbol($str,$symbol){
    if($symbol == 'img'){
    $str = preg_replace('#<'.$symbol.'[^>]+>#i','|~|', $str);
    $str = str_replace('</'.$symbol.'>', '', $str);
    }
    $str = preg_replace('#<'.$symbol.'[^>]+>#i','', $str);
    $str = str_replace('</'.$symbol.'>', '', $str);
    $str = str_replace('<'.$symbol.'>', '', $str);
    return $str;
}
function getContentFont($str){
    $str    = text_replace($str);
    $str    = del_symbol($str,'div');
    $str    = del_symbol($str,'font');
    $str    = del_symbol($str,'p');
    $str    = del_symbol($str,'a');
    $str    = del_symbol($str,'li');
    $str    = del_symbol($str,'ul');
    $str    = del_symbol($str,'span');
    $str    = del_symbol($str,'img');

    $str    = del_symbol($str,'table');
    $str    = del_symbol($str,'tr');
    $str    = del_symbol($str,'td');
    $str    = del_symbol($str, 'blockquote');

    return $str;


    $content = text_replace($content);
}
function getContent($content,$emlt){
    $res=preg_match_all('#<'.$emlt.'([^<]*)>([^<]*)</'.$emlt.'>#i', $content,$fonts);
    foreach($fonts['0'] as $key=>$value){
        $content = str_replace($value, '', $content);
        $data['data'][]=array(
                'infor'=>$fonts['2'][$key],
                'type' =>0);
    }
    $data['content'] = $content;
    return $data;
}
function getContentReplace($content,$emlt){
    $res=preg_match_all('#<'.$emlt.'([^<]*)>([^<]*)</'.$emlt.'>#i', $content,$fonts);
    foreach($fonts['0'] as $key=>$value){
        $content = str_replace($value, '', $content);
        $data['data'][]=array(
                'infor'=>$fonts['2'][$key],
                'type' =>0);
    }
    $data['content'] = $content;
    return $data;
}
function doContent($str,$attachmentAId){
    $str = text_replace($str);
    $img = get_content_img($str,$attachmentAId);
    $str = $img['content'];
    $a   = content_a($str);
    $str = $a['content'];
    $div = getContent($str,'div');
    $str = $div['content'];
    $font= getContent($str,'font');
    $str = $font['content'];
    $p= getContent($str,'p');
    $str = $p['content'];


    $data[]=$img['data'];
    $data[]=$a['data'];
    $data[]=$div['data'];
    $data[]=$font['data'];
    $data[]=$p['data'];

    foreach($data as $k=>$v){
        foreach($v as $v2){
            $message[]=$v2;
        }
    }
    return $message;
}

function get_avatar($uid, $size = 'middle', $type = '') {
    $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
    $uid = abs(intval($uid));
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    $typeadd = $type == 'real' ? '_real' : '';
    return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}

 function userIconImg($uid, $url = 'uc_server',$size = 'middle', $type){
    $oraginurl = str_replace('http://', '', UC_API);
    $oraginurl = explode('/', $oraginurl);
    $avatarPathWeb = UC_API.'/data/avatar/'.get_avatar($uid, $size, $type);
    $avatarPath = dirname(__FILE__).'/../../uc_server/data/avatar/'.get_avatar($uid, $size, $type);
    $avatar = 'data/avatar/'.get_avatar($uid, $size, $type);
    if(file_exists($avatarPath)) {
        $random = !empty($random) ? rand(1000, 9999) : '';
        $avatar_url = empty($random) ? $avatar : $avatar.'?random='.$random;
        $oraginurl = implode('/',$oraginurl);
        $oraginurl = str_replace('http:/', 'http://', $oraginurl);
        $results='http://'.$oraginurl.'/'.$avatar_url;
    }/*elseif(checkRemoteFileExists($avatarPathWeb)){
        $results =  $avatarPathWeb ;
    }*/else {
        $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
        $avatar_url = 'images/noavatar_'.$size.'.gif';
        $oraginurl = implode('/',$oraginurl);
        $oraginurl = str_replace('http:/', 'http://', $oraginurl);
        $results='http://'.$oraginurl.'/'.$avatar_url;
    }
    return $results;
}


function get_avatar_icon($uid, $size = 'middle', $type = '') {
    $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
    $uid = abs(intval($uid));
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    $typeadd = $type == 'real' ? '_real' : '';
    return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}

function getUserStatus($uids){
    $uid = implode("','",$uids);
    $limit = count($uids);
    $userList = DB::fetch_all('SELECT uid,username,status FROM %t WHERE uid in(%n) ORDER BY uid desc LIMIT %d', array('common_member', $uids, $limit), 'uid');
    return $userList;
}
function Anonymous_User($fid)
{
    $userList=DB::fetch('SELECT authorid FROM '.DB::table(forum_post).' WHERE tid= '.$fid);
    return $userList;
}
function Sel_getUserStatus($uids){
    $userList = DB::fetch_all('SELECT uid,username,status FROM %t WHERE uid =%n ORDER BY uid desc', array('common_member', $uids), 'uid');
    return $userList;
}
function sub_str($content,  $start, $length){
    if (strlen($content) > $length){
        $str = mb_substr ( $content, $start, $length,UC_DBCHARSET).'...';
    }else{
        $str = $content;
    }
    return $str;
}

function cutstrrx($Str, $Length) {
       global $s;
       $i = 0;
       $l = 0;
       $ll = strlen($Str);
       $s = $Str;
       $f = true;

      while ($i <= $ll) {
            if (ord($Str{$i}) < 0x80) {
                    $l++; $i++;
            } else if (ord($Str{$i}) < 0xe0) {
                     $l++; $i += 2;
            } else if (ord($Str{$i}) < 0xf0) {
                     $l += 2; $i += 3;
            } else if (ord($Str{$i}) < 0xf8) {
                     $l += 1; $i += 4;
            } else if (ord($Str{$i}) < 0xfc) {
                     $l += 1; $i += 5;
            } else if (ord($Str{$i}) < 0xfe) {
                     $l += 1; $i += 6;
            }

           if (($l >= $Length - 1) && $f) {
                    $s = substr($Str, 0, $i);
                    $f = false;
            }

            if (($l > $Length) && ($i < $ll)) {
                    $s = $s . '...'; break;
            }
      }
      return $s;
}

function get_avatar_path($uid) {
    $uid = abs ( intval ( $uid ) );
    $uid = sprintf ( "%09d", $uid );
    $dir1 = substr ( $uid, 0, 3 );
    $dir2 = substr ( $uid, 3, 2 );
    $dir3 = substr ( $uid, 5, 2 );
    return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
}
function get_avatar_file($uid, $type = '') {
    $uid = abs ( intval ( $uid ) );
    $uid = sprintf ( "%09d", $uid );
    $dir1 = substr ( $uid, 0, 3 );
    $dir2 = substr ( $uid, 3, 2 );
    $dir3 = substr ( $uid, 5, 2 );
    $typeadd = $type == 'real' ? '_real' : '';
    return array (
            substr ( $uid, - 2 ) . $typeadd . "_avatar_big.jpg",
            substr ( $uid, - 2 ) . $typeadd . "_avatar_middle.jpg",
            substr ( $uid, - 2 ) . $typeadd . "_avatar_small.jpg"
    );
}
 function struct_to_array($item){
    if(!is_string($item)){
        $item =(array)$item;
        foreach($item as $key=>$val){
            $item[$key]  = struct_to_array($val);
        }
        return $item;
    }else{
        $arr[]=$item;
        return $arr;
    }

}

 function xml_to_array($xml)
{
    $array =(array)(simplexml_load_string($xml));
    foreach ($array as $key=>$item){
        $array[$key]  = struct_to_array((array)$item);
    }
    return $array;
}
/*rx20130823 pan duan wang luo wen jian shi fou cun zai*/
function check_remote_file_exists($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    $result = curl_exec($curl);
    $found = false;
    if ($result !== false) {
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            $found = true;
        }
    }
    curl_close($curl);
    return $found;
}

function replaceHtmlAndJs($document)
{
    $document = trim($document);
    if (strlen($document) <= 0) {
        return $document;
    }
    $search = array ("'<script[^>]*?>.*?</script>'si",
            "'<[\/\!]*?[^<>]*?>'si",

            "'&(quot|#34);'i",
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i"
    );
    $replace = array ("",
            "",

            "\"",
            "&",
            "<",
            ">",
            " "
    );
    return @preg_replace ($search, $replace, $document);
}

function checkRemoteFileExists($file) {
    return (bool)fopen($file, 'rb');
}

function smilesReplace($atc_content){
    global $_G;
    $baseUrl = $_G['siteurl'] . '..';
    $smiles = array('phiz' => array(
            '[泪]' => '00.png',
            '[哈哈]' => '01.png',
            '[抓狂]' => '02.png',
            '[嘻嘻]' => '03.png',
            '[偷笑]' => '04.png',
            '[怒]' => '05.png',
            '[鼓掌]' => '06.png',
            '[心]' => '07.png',
            '[心碎了]' => '08.png',
            '[生病]' => '09.png',
            '[爱你]' => '10.png',
            '[害羞]' => '11.png',
            '[馋嘴]' => '12.png',
            '[可怜]' => '13.png',
            '[晕]' => '14.png',
            '[花心]' => '15.png',
            '[太开心]' => '16.png',
            '[亲亲]' => '17.png',
            '[鄙视]' => '18.png',
            '[呵呵]' => '19.png',
            '[挖鼻屎]' => '20.png',
            '[衰]' => '21.png',
            '[兔子]' => '22.png',
            '[good]' => '23.png',
            '[来]' => '24.png',
            '[威武]' => '25.png',
            '[围观]' => '26.png',
            '[萌]' => '27.png',
            '[送花]' => '28.png',
            '[囧]' => '29.png',
            '[酷]' => '30.png',
            '[糗大了]' => '31.png',
            '[撇嘴]' => '32.png',
            '[发呆]' => '33.png',
            '[汗]' => '34.png',
            '[睡]' => '35.png',
            '[吃惊]' => '36.png',
            '[白眼]' => '37.png',
            '[疑问]' => '38.png',
            '[阴险]' => '39.png',
            '[左哼哼]' => '40.png',
            '[右哼哼]' => '41.png',
            '[敲打]' => '42.png',
            '[委屈]' => '43.png',
            '[嘘]' => '44.png',
            '[吐]' => '45.png',
            '[做鬼脸]' => '46.png',
            '[ByeBye]' => '47.png',
            '[要哭了]' => '48.png',
            '[傲慢]' => '49.png',
            '[月亮]' => '50.png',
            '[太阳]' => '51.png',
            '[耶]' => '52.png',
            '[握手]' => '53.png',
            '[ok]' => '54.png',
            '[饭]' => '55.png',
            '[咖啡]' => '56.png',
            '[礼物]' => '57.png',
            '[猪头]' => '58.png',
            '[抱抱]' => '59.png',
            '[赞]' => '60.png',
            '[Hold]' => '61.png',
            '[神马]' => '62.png',
            '[坑爹]' => '63.png',
            '[有木有]' => '64.png',
            '[谢谢]' => '65.png',
            '[蓝心]' => '66.png',
            '[外星人]' => '67.png',
            '[魔鬼]' => '68.png',
            '[紫心]' => '69.png',
            '[绿心]' => '70.png',
            '[黄心]' => '71.png',
            '[音符]' => '72.png',
            '[闪烁]' => '73.png',
            '[星星]' => '74.png',
            '[雨滴]' => '75.png',
            '[火焰]' => '76.png',
            '[便便]' => '77.png',
            '[踩一脚]' => '78.png',
            '[下雨]' => '79.png',
            '[多云]' => '80.png',
            '[闪电]' => '81.png',
            '[雪花]' => '82.png',
            '[旋风]' => '83.png',
            '[包]' => '84.png',
            '[房子]' => '85.png',
            '[烟花]' => '86.png'
    ),);
    if(UC_DBCHARSET == 'gbk'){
        $smiles['phiz'] = arrayCoding($smiles['phiz'],"utf-8","gbk");
    }
    preg_match_all('/\[.*?\]/',$atc_content,$res);
    foreach($res[0] as $k => $v){
        foreach($smiles['phiz'] as $key => $val){
            if($v == $key){
                $atc_content = str_replace($v,"[img]".$baseUrl.'/app/data/phiz/default/'.$val."[/img]",$atc_content);
            }
        }
    }
    return $atc_content;
}
function arrayCoding ($array, $inCharset, $outCharset) {
    if (!is_array($array))
        return false;
    foreach ($array as $key => $value) {
        $key2 = iconv($inCharset, $outCharset, $key);
        $array[$key2] = $value;
        unset($array[$key]);
    }
    return $array;
}
/*
function checkRemoteFileExists($file) {
    $url2 = $file;
    $ch = curl_init();
    $timeout = 10;
    curl_setopt ($ch, CURLOPT_URL, $url2);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $contents = curl_exec($ch);
    //echo $contents;
    if (preg_match("/HTTP\/1.1 200/", $contents) || preg_match("/HTTP\/1.1 304/", $contents)){
        return true;
    }else {
        return false;
    }
} */

class CJSON {
    const JSON_SLICE = 1;
    const JSON_IN_STR = 2;
    const JSON_IN_ARR = 4;
    const JSON_IN_OBJ = 8;
    const JSON_IN_CMT = 16;

    static function encode($var) {
        global $_G;
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'NULL':
                return 'null';
            case 'integer':
                return (int) $var;
            case 'double':
            case 'float':
                return rtrim(sprintf('%.16F',$var),'0');
            case 'string':
                if(function_exists('diconv') && strtolower($_G['charset']) != 'utf-8') {
                    $var = diconv($var, $_G['charset'], 'utf-8');
                }
                if(function_exists('json_encode')) {
                    return json_encode($var);
                }
                $ascii = '';
                $strlen_var = strlen($var);
                for ($c = 0; $c < $strlen_var; ++$c) {
                    $ord_var_c = ord($var{$c});
                    switch (true) {
                        case $ord_var_c == 0x08:
                            $ascii .= '\b';
                            break;
                        case $ord_var_c == 0x09:
                            $ascii .= '\t';
                            break;
                        case $ord_var_c == 0x0A:
                            $ascii .= '\n';
                            break;
                        case $ord_var_c == 0x0C:
                            $ascii .= '\f';
                            break;
                        case $ord_var_c == 0x0D:
                            $ascii .= '\r';
                            break;

                        case $ord_var_c == 0x22:
                        case $ord_var_c == 0x2F:
                        case $ord_var_c == 0x5C:
                            $ascii .= '\\'.$var{$c};
                            break;

                        case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                            $ascii .= $var{$c};
                            break;

                        case (($ord_var_c & 0xE0) == 0xC0):
                            $char = pack('C*', $ord_var_c, ord($var{$c+1}));
                            $c+=1;
                            $utf16 =  self::utf8ToUTF16BE($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF0) == 0xE0):
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c+1}),
                                         ord($var{$c+2}));
                            $c+=2;
                            $utf16 = self::utf8ToUTF16BE($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF8) == 0xF0):
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c+1}),
                                         ord($var{$c+2}),
                                         ord($var{$c+3}));
                            $c+=3;
                            $utf16 = self::utf8ToUTF16BE($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFC) == 0xF8):
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c+1}),
                                         ord($var{$c+2}),
                                         ord($var{$c+3}),
                                         ord($var{$c+4}));
                            $c+=4;
                            $utf16 = self::utf8ToUTF16BE($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFE) == 0xFC):
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c+1}),
                                         ord($var{$c+2}),
                                         ord($var{$c+3}),
                                         ord($var{$c+4}),
                                         ord($var{$c+5}));
                            $c+=5;
                            $utf16 = self::utf8ToUTF16BE($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;
                    }
                }

                return '"'.$ascii.'"';

            case 'array':
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
                    return '{' .
                           join(',', array_map(array('CJSON', 'nameValue'),
                                               array_keys($var),
                                               array_values($var)))
                           . '}';
                }

                return '[' . join(',', array_map(array('CJSON', 'encode'), $var)) . ']';
            case 'object':
                if ($var instanceof Traversable)
                {
                    $vars = array();
                    foreach ($var as $k=>$v)
                        $vars[$k] = $v;
                }
                else
                    $vars = get_object_vars($var);
                return '{' .
                       join(',', array_map(array('CJSON', 'nameValue'),
                                           array_keys($vars),
                                           array_values($vars)))
                       . '}';

            default:
                return '';
        }
    }

    static function nameValue($name, $value) {
        return self::encode(strval($name)) . ':' . self::encode($value);
    }

    static function reduceString($str) {
        $str = preg_replace(array(

                '#^\s*//(.+)$#m',

                '#^\s*/\*(.+)\*/#Us',

                '#/\*(.+)\*/\s*$#Us'

            ), '', $str);

        return trim($str);
    }

    static function decode($str, $useArray=true) {
        if(function_exists('json_decode')) {
            return json_decode($str, $useArray);
            }

        $str = self::reduceString($str);

        switch (strtolower($str)) {
            case 'true':
                return true;

            case 'false':
                return false;

            case 'null':
                return null;

            default:
                if (is_numeric($str)) {
                    return ((float)$str == (integer)$str)
                        ? (integer)$str
                        : (float)$str;

                } elseif (preg_match('/^("|\').+(\1)$/s', $str, $m) && $m[1] == $m[2]) {

                    $delim = substr($str, 0, 1);
                    $chrs = substr($str, 1, -1);
                    $utf8 = '';
                    $strlen_chrs = strlen($chrs);

                    for ($c = 0; $c < $strlen_chrs; ++$c) {

                        $substr_chrs_c_2 = substr($chrs, $c, 2);
                        $ord_chrs_c = ord($chrs{$c});

                        switch (true) {
                            case $substr_chrs_c_2 == '\b':
                                $utf8 .= chr(0x08);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\t':
                                $utf8 .= chr(0x09);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\n':
                                $utf8 .= chr(0x0A);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\f':
                                $utf8 .= chr(0x0C);
                                ++$c;
                                break;
                            case $substr_chrs_c_2 == '\r':
                                $utf8 .= chr(0x0D);
                                ++$c;
                                break;

                            case $substr_chrs_c_2 == '\\"':
                            case $substr_chrs_c_2 == '\\\'':
                            case $substr_chrs_c_2 == '\\\\':
                            case $substr_chrs_c_2 == '\\/':
                                if (($delim == '"' && $substr_chrs_c_2 != '\\\'') ||
                                   ($delim == "'" && $substr_chrs_c_2 != '\\"')) {
                                    $utf8 .= $chrs{++$c};
                                }
                                break;

                            case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6)):
                                $utf16 = chr(hexdec(substr($chrs, ($c+2), 2)))
                                       . chr(hexdec(substr($chrs, ($c+4), 2)));
                                $utf8 .= self::utf16beToUTF8($utf16);
                                $c+=5;
                                break;

                            case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
                                $utf8 .= $chrs{$c};
                                break;

                            case ($ord_chrs_c & 0xE0) == 0xC0:
                                $utf8 .= substr($chrs, $c, 2);
                                ++$c;
                                break;

                            case ($ord_chrs_c & 0xF0) == 0xE0:
                                $utf8 .= substr($chrs, $c, 3);
                                $c += 2;
                                break;

                            case ($ord_chrs_c & 0xF8) == 0xF0:
                                $utf8 .= substr($chrs, $c, 4);
                                $c += 3;
                                break;

                            case ($ord_chrs_c & 0xFC) == 0xF8:
                                $utf8 .= substr($chrs, $c, 5);
                                $c += 4;
                                break;

                            case ($ord_chrs_c & 0xFE) == 0xFC:
                                $utf8 .= substr($chrs, $c, 6);
                                $c += 5;
                                break;

                        }

                    }

                    return $utf8;

                } elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {

                    if ($str{0} == '[') {
                        $stk = array(self::JSON_IN_ARR);
                        $arr = array();
                    } else {
                        if ($useArray) {
                            $stk = array(self::JSON_IN_OBJ);
                            $obj = array();
                        } else {
                            $stk = array(self::JSON_IN_OBJ);
                            $obj = new stdClass();
                        }
                    }

                    array_push($stk, array('what'  => self::JSON_SLICE,
                                           'where' => 0,
                                           'delim' => false));

                    $chrs = substr($str, 1, -1);
                    $chrs = self::reduceString($chrs);

                    if ($chrs == '') {
                        if (reset($stk) == self::JSON_IN_ARR) {
                            return $arr;

                        } else {
                            return $obj;

                        }
                    }

                    $strlen_chrs = strlen($chrs);

                    for ($c = 0; $c <= $strlen_chrs; ++$c) {

                        $top = end($stk);
                        $substr_chrs_c_2 = substr($chrs, $c, 2);

                        if (($c == $strlen_chrs) || (($chrs{$c} == ',') && ($top['what'] == self::JSON_SLICE))) {

                            $slice = substr($chrs, $top['where'], ($c - $top['where']));
                            array_push($stk, array('what' => self::JSON_SLICE, 'where' => ($c + 1), 'delim' => false));
                            if (reset($stk) == self::JSON_IN_ARR) {
                                array_push($arr, self::decode($slice,$useArray));

                            } elseif (reset($stk) == self::JSON_IN_OBJ) {
                                if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                    $key = self::decode($parts[1],$useArray);
                                    $val = self::decode($parts[2],$useArray);

                                    if ($useArray) {
                                        $obj[$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                } elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                    $key = $parts[1];
                                    $val = self::decode($parts[2],$useArray);

                                    if ($useArray) {
                                        $obj[$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                }

                            }

                        } elseif ((($chrs{$c} == '"') || ($chrs{$c} == "'")) && ($top['what'] != self::JSON_IN_STR)) {
                            array_push($stk, array('what' => self::JSON_IN_STR, 'where' => $c, 'delim' => $chrs{$c}));
                        } elseif (($chrs{$c} == $top['delim']) &&
                                 ($top['what'] == self::JSON_IN_STR) &&
                                 (($chrs{$c - 1} != "\\") ||
                                 ($chrs{$c - 1} == "\\" && $chrs{$c - 2} == "\\"))) {
                            array_pop($stk);
                        } elseif (($chrs{$c} == '[') &&
                                 in_array($top['what'], array(self::JSON_SLICE, self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
                            array_push($stk, array('what' => self::JSON_IN_ARR, 'where' => $c, 'delim' => false));
                        } elseif (($chrs{$c} == ']') && ($top['what'] == self::JSON_IN_ARR)) {
                            array_pop($stk);
                        } elseif (($chrs{$c} == '{') &&
                                 in_array($top['what'], array(self::JSON_SLICE, self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
                            array_push($stk, array('what' => self::JSON_IN_OBJ, 'where' => $c, 'delim' => false));
                        } elseif (($chrs{$c} == '}') && ($top['what'] == self::JSON_IN_OBJ)) {
                            array_pop($stk);
                        } elseif (($substr_chrs_c_2 == '/**') &&
                                 in_array($top['what'], array(self::JSON_SLICE, self::JSON_IN_ARR, self::JSON_IN_OBJ))) {
                            array_push($stk, array('what' => self::JSON_IN_CMT, 'where' => $c, 'delim' => false));
                            $c++;
                        } elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == self::JSON_IN_CMT)) {
                            array_pop($stk);
                            $c++;
                            for ($i = $top['where']; $i <= $c; ++$i) {
                                $chrs = substr_replace($chrs, ' ', $i, 1);
                                          }
                        }

                    }

                    if (reset($stk) == self::JSON_IN_ARR) {
                        return $arr;

                    } elseif (reset($stk) == self::JSON_IN_OBJ) {
                        return $obj;

                    }

                }
        }
    }


    static function utf8ToUnicode( &$str ) {
        $unicode = array();
        $values = array();
        $lookingFor = 1;

        for ($i = 0; $i < strlen( $str ); $i++ ) {
            $thisValue = ord( $str[ $i ] );
            if ( $thisValue < 128 ) {
                $unicode[] = $thisValue;
                  } else {
                if ( count( $values ) == 0 ) {
                    $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
                        }
                $values[] = $thisValue;
                if ( count( $values ) == $lookingFor ) {
                    $number = ( $lookingFor == 3 ) ?
                        ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                        ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
                    $unicode[] = $number;
                    $values = array();
                    $lookingFor = 1;
                }
            }
        }
        return $unicode;
    }

    static function unicodeToUTF8( &$str )
    {
        $utf8 = '';
        foreach( $str as $unicode )
        {
            if ( $unicode < 128 )
            {
                $utf8.= chr( $unicode );
            }
            elseif ( $unicode < 2048 )
            {
                $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
                $utf8.= chr( 128 + ( $unicode % 64 ) );
            }
            else
            {
                $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
                $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
                $utf8.= chr( 128 + ( $unicode % 64 ) );
            }
        }
        return $utf8;
    }

    static function utf8ToUTF16BE(&$str, $bom = false) {
        $out = $bom ? "\xFE\xFF" : '';
        if(function_exists('mb_convert_encoding'))
            return $out.mb_convert_encoding($str,'UTF-16BE','UTF-8');

        $uni = self::utf8ToUnicode($str);
        foreach($uni as $cp)
            $out .= pack('n',$cp);
        return $out;
    }

    static function utf16beToUTF8(&$str) {
        $uni = unpack('n*',$str);
        return self::unicodeToUTF8($uni);
    }
}