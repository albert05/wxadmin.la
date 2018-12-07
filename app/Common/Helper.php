<?php

/**
 * 公共方法
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:01
 */

namespace App\Common;

USE DB;

class Helper
{

    /**
     * 加锁
     * @param $name
     * @return bool
     */
    public static function Lock($name) {
        $lock_file = '/tmp/_la_lock_console_' . $name . '.log';

        if (file_exists($lock_file)) {
            return false;
        }

        return touch($lock_file);
    }

    /**
     * 解锁
     * @param $name
     * @return bool
     */
    public static function unlock($name) {
        $lock_file = '/tmp/_la_lock_console_' . $name . '.log';

        if (!file_exists($lock_file)) {
            return false;
        }

        return unlink($lock_file);
    }

    /**
     * 过滤命令参数
     * @param $signature
     * @return string
     */
    public static function filterSignature($signature) {
        return trim(preg_replace('/\{(.*)\}/', '', $signature));
    }

    /**
     * 获取当前毫秒级时间
     * @return float
     */
    public static function getMicrotime() {
        list($usec, $sec) = explode(" ", microtime());
        return floatval(date("Gis")) + $usec;
    }

    /**
     * 转换时间格式
     * 转换时11/01/2017 12:00 AM间格式
     * @param $timeStr
     */
    public static function analyzeTimeStr($timeStr) {
        return date("Y-m-d H:i:s", strtotime(preg_replace('/^(\d{2})\/(\d{2})\/(\d{4})(.*)$/', '$3\/$1\/$2$4', $timeStr)));
    }


}