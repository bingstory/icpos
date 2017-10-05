<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function welcome_get_last_modules() {
	load()->classs('cloudapi');

	$api = new CloudApi();
	$last_modules = $api->get('store', 'app_fresh');
	return $last_modules;
}


function welcome_notices_get() {
	$notices = pdo_getall('article_notice', array('is_display' => 1), array('id', 'title', 'createtime'), '', 'createtime DESC', array(1,5));
	if(!empty($notices)) {
		foreach ($notices as $key => $notice_val) {
			$notices[$key]['url'] = url('article/notice-show/detail', array('id' => $notice_val['id']));
			$notices[$key]['createtime'] = date('Y-m-d', $notice_val['createtime']);
		}
	}
	return $notices;
}
