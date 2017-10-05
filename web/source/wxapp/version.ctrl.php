<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('module');
load()->model('wxapp');

$dos = array('display', 'manage', 'module_link_uniacid', 'search_link_account', 'module_unlink_uniacid');
$do = in_array($do, $dos) ? $do : 'display';
if ($do == 'module_link_uniacid') {
	uni_user_permission_check('wxapp_module_link_uniacid', true, 'wxapp');
}
$_W['page']['title'] = '小程序 - 管理';

$uniacid = intval($_GPC['uniacid']);
$version_id = intval($_GPC['version_id']);
if (!empty($uniacid)) {
	$wxapp_info = wxapp_fetch($uniacid);
}
if (!empty($version_id)) {
	$version_info = wxapp_version($version_id);
	$wxapp_info = wxapp_fetch($version_info['uniacid']);
}

if ($do == 'display') {
	$wxapp_version_list = wxapp_version_all($uniacid);
	template('wxapp/version-display');
}

if ($do == 'manage') {
	if ($version_info['design_method'] == WXAPP_TEMPLATE) {
		$version_site_info = wxapp_site_info($version_info['multiid']);
	}
	$role = uni_permission($_W['uid'], $wxapp_info['uniacid']);
	template('wxapp/version-manage');
}

if ($do == 'module_link_uniacid') {
	$module_name = $_GPC['module_name'];
	
	$version_info = wxapp_version($version_id);

	if (checksubmit('submit')) {
		if (empty($module_name) || empty($uniacid)) {
			iajax('1', '参数错误！');
		}
		$module = module_fetch($module_name);
		if (empty($module)) {
			iajax('1', '模块不存在！');
		}
		$module_update = array();
		$module_update[$module['name']] = array('name' => $module['name'], 'version' => $module['version'], 'uniacid' => $uniacid);
		pdo_update('wxapp_versions', array('modules' => serialize($module_update)), array('id' => $version_id));
		iajax(0, '关联公众号成功');
	}
	template('wxapp/version-module-link-uniacid');
}

if ($do == 'module_unlink_uniacid') {
	if (!empty($version_info)) {
		$module = current($version_info['modules']);
		$version_modules = array(
				$module['name'] => array(
					'name' => $module['name'],
					'version' => $module['version']
					)
			);
	}
	$version_modules = serialize($version_modules);
	$result = pdo_update('wxapp_versions', array('modules' => $version_modules), array('id' => $version_info['id']));
	if ($result) {
		itoast('删除成功！', referer(), 'success');
	} else {
		itoast('删除失败！', referer(), 'error');
	}
}

if ($do == 'search_link_account') {
	$module_name = $_GPC['module_name'];
	if (empty($module_name)) {
		iajax(0, array());
	}
	$account_list = uni_owned();
	if ($_W['isfounder']) {
		iajax(0, $account_list);
	}
	if (!empty($account_list)) {
		foreach ($account_list as $i => $account) {
			$has_modules = uni_modules_by_uniacid($account['uniacid']);
			if (empty($has_modules[$module_name])) {
				unset($account_list[$i]);
			}
		}
	}
	iajax(0, $account_list);
}
