<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('rank', 'display', 'switch');
$do = in_array($_GPC['do'], $dos)? $do : 'display' ;
$_W['page']['title'] = '公众号列表 - 公众号';

$state = uni_permission($_W['uid'], $_W['uniacid']);
$account_info = uni_user_account_permission();

if($do == 'switch') {
	$uniacid = intval($_GPC['uniacid']);
	$role = uni_permission($_W['uid'], $uniacid);
	if(empty($role)) {
		itoast('操作失败, 非法访问.', '', 'error');
	}

	uni_account_save_switch($uniacid);
	uni_account_switch($uniacid,  url('home/welcome'));
}

if ($do == 'rank' && $_W['isajax'] && $_W['ispost']) {
	$uniacid = intval($_GPC['id']);

	$exist = pdo_get('uni_account', array('uniacid' => $uniacid));
	if (empty($exist)) {
		iajax(1, '公众号不存在', '');
	}
	uni_account_rank_top($uniacid);
	iajax(0, '更新成功！', '');
}

if ($do == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 8;
	$start = ($pindex - 1) * $psize;

	$condition = '';
	$param = array();
	$keyword = trim($_GPC['keyword']);
	if (!empty($_W['isfounder'])) {
		$condition .= " WHERE a.default_acid <> 0 AND b.isdeleted <> 1 AND (b.type = ".ACCOUNT_TYPE_OFFCIAL_NORMAL." OR b.type = ".ACCOUNT_TYPE_OFFCIAL_AUTH.")";
		$order_by = " ORDER BY a.`rank` DESC";
	} else {
		$condition .= "LEFT JOIN ". tablename('uni_account_users')." as c ON a.uniacid = c.uniacid WHERE a.default_acid <> 0 AND c.uid = :uid AND b.isdeleted <> 1 AND (b.type = ".ACCOUNT_TYPE_OFFCIAL_NORMAL." OR b.type = ".ACCOUNT_TYPE_OFFCIAL_AUTH.")";
		$param[':uid'] = $_W['uid'];
		$order_by = " ORDER BY c.`rank` DESC";
	}
	if(!empty($keyword)) {
		$condition .=" AND a.`name` LIKE :name";
		$param[':name'] = "%{$keyword}%";
	}
	if(isset($_GPC['letter']) && strlen($_GPC['letter']) == 1) {
		$letter = trim($_GPC['letter']);
		if(!empty($letter)){
			$condition .= " AND a.`title_initial` = :title_initial";
			$param[':title_initial'] = $letter;
		}else {
			$condition .= " AND a.`title_initial` = ''";
		}
	}

	$tsql = "SELECT COUNT(*) FROM " . tablename('uni_account'). " as a LEFT JOIN". tablename('account'). " as b ON a.default_acid = b.acid {$condition} {$order_by}, a.`uniacid` DESC";
	$total = pdo_fetchcolumn($tsql, $param);
	$sql = "SELECT * FROM ". tablename('uni_account'). " as a LEFT JOIN". tablename('account'). " as b ON a.default_acid = b.acid  {$condition} {$order_by}, a.`uniacid` DESC LIMIT {$start}, {$psize}";
	$list = pdo_fetchall($sql, $param);
	if(!empty($list)) {
		foreach($list as &$account) {
			$account['url'] = url('account/display/switch', array('uniacid' => $account['uniacid']));
			$account['details'] = uni_accounts($account['uniacid']);
			if(!empty($account['details'])) {
				foreach ($account['details'] as  &$account_val) {
					$account_val['thumb'] = tomedia('headimg_'.$account_val['acid']. '.jpg').'?time='.time();
				}
			}
			$account['role'] = uni_permission($_W['uid'], $account['uniacid']);
			$account['setmeal'] = uni_setmeal($account['uniacid']);
		}
		unset($account_val);
		unset($account);
	}
	$pager = pagination($total, $pindex, $psize);
}

template('account/display');