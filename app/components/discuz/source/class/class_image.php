<?php

require_once libfile('class/image');

/**
 * 图像类，继承于discuz的image类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>  
 */

class discuz_image extends image {}

class Mobcent_Image extends discuz_image {

    public function makeWatermark($source, $target = '', $type = 'forum') {
        $return = $this->init('watermask', $source, $target);
        if($return <= 0) {
            return $this->returncode($return);
        }

        if(!$this->param['watermarkstatus'][$type] || ($this->param['watermarkminwidth'][$type] && $this->imginfo['width'] <= $this->param['watermarkminwidth'][$type] && $this->param['watermarkminheight'][$type] && $this->imginfo['height'] <= $this->param['watermarkminheight'][$type])) {
            return $this->returncode(0);
        }
        $this->param['watermarkfile'][$type] = './static/image/common/'.($this->param['watermarktype'][$type] == 'png' ? 'watermark.png' : 'watermark.gif');
        
        // 修改成正确的绝对路径
        $this->param['watermarkfile'][$type] = DISCUZ_ROOT. '/static/image/common/'.($this->param['watermarktype'][$type] == 'png' ? 'watermark.png' : 'watermark.gif');
        !empty($this->param['watermarktext']['fontpath'][$type]) && $this->param['watermarktext']['fontpath'][$type] = DISCUZ_ROOT.'/'.$this->param['watermarktext']['fontpath'][$type];

        if(!is_readable($this->param['watermarkfile'][$type]) || ($this->param['watermarktype'][$type] == 'text' && (!file_exists($this->param['watermarktext']['fontpath'][$type]) || !is_file($this->param['watermarktext']['fontpath'][$type])))) {
            return $this->returncode(-3);
        }

        $return = !$this->libmethod ? $this->Watermark_GD($type) : $this->Watermark_IM($type);

        return $this->sleep($return);
    }

    public function makeThumb($source, $target, $thumbwidth, $thumbheight = 0, $thumbtype = 1, $nosuffix = 1) {
        $return = $this->init('thumb', $source, $target, $nosuffix);
        if($return <= 0) {
            return $this->returncode($return);
        }

        if($this->imginfo['animated']) {
            return $this->returncode(0);
        }
        $this->param['thumbwidth'] = intval($thumbwidth);
        if(!$thumbheight || $thumbheight > $this->imginfo['height']) {
            $thumbheight = $thumbwidth > $this->imginfo['width'] ? $this->imginfo['height'] : $this->imginfo['height']*($thumbwidth/$this->imginfo['width']);
        }
        $this->param['thumbheight'] = intval($thumbheight);
        $this->param['thumbtype'] = $thumbtype;
        if($thumbwidth < 100 && $thumbheight < 100) {
            $this->param['thumbquality'] = 100;
        }

        $return = !$this->libmethod ? $this->Thumb_GD() : $this->Thumb_IM();
        // $return = !$nosuffix ? $return : 0;

        return $this->sleep($return);
    }
}