<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/25
 * Time: 20:13
 */
function get_order_sn()
{
    $time = explode(' ', microtime());
    $time = $time[1] . $time[0] * 1000;
    $time = explode('.', $time);
    $time = isset($time[1]) ? $time[1] : 0;
    $time = date('YmdHis') + $time;
    mt_srand((double) microtime() * 1000000);
    return $time . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}