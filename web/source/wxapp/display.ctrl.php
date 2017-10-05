<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('wxapp');
load()->model('account');

$_W['page']['title'] = '小程序列表';

$dos = array('display', 'switch', 'rank' , 'home');
$do = in_array($do, $dos) ? $do : 'display';

if ($do == 'rank' || $do == 'switch') {
	$uniacid = intval($_GPC['uniacid']);
	if (!empty($uniacid)) {
		$wxapp_info = wxapp_fetch($uniacid);
		if (empty($wxapp_info)) {
			itoast('小程序不存在', referer(), 'error');
		}
	}
}
if ($do == 'home') {
	$last_uniacid = uni_account_last_switch();
	if (empty($last_uniacid)) {
		itoast('', url('wxapp/display'), 'info');
	} else {
		$last_version = wxapp_fetch($last_uniacid);
		if (!empty($last_version)) {
			uni_account_switch($last_uniacid);
			header('Location: ' . url('wxapp/version/manage', array('version_id' => $last_version['version']['id'])));
			exit;
		} else {
			itoast('', url('wxapp/display'), 'info');
		}
	}
} elseif ($do == 'display') {
		$account_info = uni_user_account_permission();
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$start = ($pindex - 1) * $psize;
	
	$condition = '';
	$param = array();
	$keyword = trim($_GPC['keyword']);
	if (!empty($_W['isfounder'])) {
		$condition .= " WHERE a.default_acid <> 0 AND b.isdeleted <> 1 AND b.type = " . ACCOUNT_TYPE_APP_NORMAL;
		$order_by = " ORDER BY a.`rank` DESC";
	} else {
		$condition .= "LEFT JOIN ". tablename('uni_account_users')." as c ON a.uniacid = c.uniacid WHERE a.default_acid <> 0 AND c.uid = :uid AND b.isdeleted <> 1 AND b.type = " . ACCOUNT_TYPE_APP_NORMAL;
		$param[':uid'] = $_W['uid'];
		$order_by = " ORDER BY c.`rank` DESC";
	}

	if (!empty($keyword)) {
		$condition .=" AND a.`name` LIKE :name";
		$param[':name'] = "%{$keyword}%";
	}
	if (isset($_GPC['letter']) && strlen($_GPC['letter']) == 1) {
		$letter = trim($_GPC['letter']);
		if (!empty($letter)) {
			$condition .= " AND a.`title_initial` = :title_initial";
			$param[':title_initial'] = $letter;
		} else {
			$condition .= " AND a.`title_initial` = ''";
		}
	}
	$tsql = "SELECT COUNT(*) FROM " . tablename('uni_account'). " as a LEFT JOIN ". tablename('account'). " as b ON a.default_acid = b.acid {$condition} {$order_by}, a.`uniacid` DESC";
	$sql = "SELECT * FROM ". tablename('uni_account'). " as a LEFT JOIN ". tablename('account'). " as b ON a.default_acid = b.acid  {$condition} {$order_by}, a.`uniacid` DESC LIMIT {$start}, {$psize}";
	$total = pdo_fetchcolumn($tsql, $param);
	$wxapp_lists = pdo_fetchall($sql, $param);
	if (!empty($wxapp_lists)) {
		$wxapp_cookie_uniacids = array();
		if (!empty($_GPC['__wxappversionids'])) {
			$wxappversionids = json_decode(htmlspecialchars_decode($_GPC['__wxappversionids']), true);
			foreach ($wxappversionids as $version_val) {
				$wxapp_cookie_uniacids[] = $version_val['uniacid'];
			}
		}
		foreach ($wxapp_lists as &$account) {
			$account['thumb'] = tomedia('headimg_'.$account['acid']. '.jpg').'?time='.time();
			$account['versions'] = wxapp_get_some_lastversions($account['uniacid']);
			$account['current_version'] = array();
			if (!empty($account['versions'])) {
				foreach ($account['versions'] as $version) {
					if (!empty($wxapp_cookie_uniacids) && !empty($wxappversionids[$version['uniacid']]) && in_array($version['id'], $wxappversionids[$version['uniacid']])) {
						$account['current_version'] = $version;
						break;
					}
				}
				if (empty($account['current_version'])) {
					$account['current_version'] = $account['versions'][0];
				}
			}
		}
		unset($account_val);
		unset($account);
	}
	$pager = pagination($total, $pindex, $psize);
	template('wxapp/account-display');
} elseif ($do == 'switch') {
	$module_name = trim($_GPC['module']);
	$version_id = !empty($_GPC['version_id']) ? intval($_GPC['version_id']) : $wxapp_info['version']['id'];
	if (!empty($module_name) && !empty($version_id)) {
		$version_info = wxapp_version($version_id);
		$module_info = array();
		if (!empty($version_info['modules'])) {
			foreach ($version_info['modules'] as $key => $module_val) {
				if ($module_val['name'] == $module_name) {
					$module_info = $module_val;
				}
			}
		}
		if (empty($version_id) || empty($module_info)) {
			itoast('版本信息错误');
		}
		$uniacid = !empty($module_info['account']['uniacid']) ? $module_info['account']['uniacid'] : $version_info['uniacid'];
		uni_account_switch($uniacid, url('home/welcome/ext/', array('m' => $module_name)));
	}
	uni_account_switch($uniacid);
	wxapp_save_switch($uniacid);
	wxapp_update_last_use_version($uniacid, $version_id);
	header('Location: ' . url('wxapp/version/manage', array('version_id' => $version_id)));
	exit;
} elseif ($do == 'rank') {
	uni_account_rank_top($uniacid);
	itoast('更新成功', '', '');
}