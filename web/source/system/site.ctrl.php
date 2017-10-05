<?php 
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('copyright');
$do = in_array($do, $dos) ? $do : 'copyright';
$_W['page']['title'] = '站点设置 - 工具  - 系统管理';

$settings = $_W['setting']['copyright'];

if(empty($settings) || !is_array($settings)) {
	$settings = array();
} else {
	$settings['slides'] = iunserializer($settings['slides']);
}

$path = IA_ROOT . '/web/themes/';
if(is_dir($path)) {
	if ($handle = opendir($path)) {
		while (false !== ($templatepath = readdir($handle))) {
			if ($templatepath != '.' && $templatepath != '..') {
				if(is_dir($path.$templatepath)){
					$template[] = $templatepath;
				}
			}
		}
	}
}
if ($do == 'copyright') {
	if (checksubmit('submit')) {
		$data = array(
			'status' => $_GPC['status'],
			'verifycode' => $_GPC['verifycode'],
			'reason' => $_GPC['reason'],
			'sitename' => $_GPC['sitename'],
			'url' => (strexists($_GPC['url'], 'http://') || strexists($_GPC['url'], 'https://')) ? $_GPC['url'] : "http://{$_GPC['url']}",
			'statcode' => htmlspecialchars_decode($_GPC['statcode']),
			'footerleft' => htmlspecialchars_decode($_GPC['footerleft']),
			'footerright' => htmlspecialchars_decode($_GPC['footerright']),
			'icon' => $_GPC['icon'],
			'flogo1' => $_GPC['flogo1'],
			'flogo2' => $_GPC['flogo2'],
			'slides' => iserializer($_GPC['slides']),
			'notice' => $_GPC['notice'],
			'blogo' => $_GPC['blogo'],
			'qrcode' => $_GPC['qrcode'],
			'baidumap' => $_GPC['baidumap'],
			'company' => $_GPC['company'],
			'skin' => (!empty($_GPC['skin']) && $_GPC['skin'] == 'black') ? 'black' : 'default',
			'companyprofile' => htmlspecialchars_decode($_GPC['companyprofile']),
			'address' => $_GPC['address'],
			'person' => $_GPC['person'],
			'phone' => $_GPC['phone'],
			'qq' => $_GPC['qq'],
			'email' => $_GPC['email'],
			'keywords' => $_GPC['keywords'],
			'description' => $_GPC['description'],
			'showhomepage' => intval($_GPC['showhomepage']),
			'leftmenufixed' => (!empty($_GPC['leftmenu_fixed'])) ? 1 : 0,
		);
		$test = setting_save($data, 'copyright');
		$template = trim($_GPC['template']);
		setting_save(array('template' => $template), 'basic');
		itoast('更新设置成功！', url('system/site'), 'success');
	}
}

template('system/site');