<?php

/**
 * 计时工具类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

class CountTimer {
    
    private $startTime;
    private $stopTime;
    
    public function __construct($autoStart = true) {
        if ($autoStart) {
            $this->start();
        }
    }

    public function start() {
        $this->startTime = $this->_microtimeFloat();
    }
    
    public function stop() {
        $this->stopTime = $this->_microtimeFloat();
        return $this->stopTime - $this->startTime;
    }
    
    private function _microtimeFloat() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}