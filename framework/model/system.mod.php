<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function system_menu_permission_list($role = '') {
	$system_menu = cache_load('system_frame');
	if(empty($system_menu)) {
		cache_build_frame_menu();
		$system_menu = cache_load('system_frame');
	}
		if ($role == ACCOUNT_MANAGE_NAME_OPERATOR) {
		unset($system_menu['appmarket']);
		unset($system_menu['adviertisement']);
		unset($system_menu['system']);
	} if ($role == ACCOUNT_MANAGE_NAME_OPERATOR) {
		unset($system_menu['appmarket']);
		unset($system_menu['adviertisement']);
	}
	return $system_menu;
}