<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('display', 'post', 'del');
$do = !empty($_GPC['do']) ? $_GPC['do'] : 'display';

if ($do == 'display') {
	uni_user_permission_check('system_user_group');
	$_W['page']['title'] = '用户组列表 - 用户组 - 用户管理';
	$condition = '' ;
	$params = array();
	if (!empty($_GPC['name'])) {
		$condition .= "WHERE name LIKE :name";
		$params[':name'] = "%{$_GPC['name']}%";
	}
	if (checksubmit('submit')) {
		if (!empty($_GPC['delete'])) {
			pdo_query("DELETE FROM ".tablename('users_group')." WHERE id IN ('".implode("','", $_GPC['delete'])."')");
		}
		itoast('用户组更新成功！', referer(), 'success');
	}
	$module_num = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('modules') . "WHERE type = :type AND issystem = :issystem", array(':type' => 'system','issystem' => 1));
	$lists = pdo_fetchall("SELECT * FROM " . tablename('users_group').$condition, $params);
	if (!empty($lists)) {
		foreach ($lists as $key => $group) {
			$package = iunserializer($group['package']);
			$group['package'] = uni_groups($package);
			if (empty($package)) {
				$lists[$key]['module_nums'] = '系统默认';
				$lists[$key]['wxapp_nums'] = '系统默认';
				continue;
			}
			if (is_array($package) && in_array(-1, $package)) {
				$lists[$key]['module_nums'] = -1;
				$lists[$key]['wxapp_nums'] = -1;
				continue;
			}
			$names = array();
			if (!empty($group['package'])) {
				foreach ($group['package'] as $modules) {
					$names[] = $modules['name'];
					$lists[$key]['module_nums'] = count($modules['modules']);
					$lists[$key]['wxapp_nums'] = count($modules['wxapp']);
				}
			}else {
				pdo_update('users_group', array('package' => 'N;'), array('id' => $group['id']));
			}

			$lists[$key]['packages'] = implode(',', $names);
		}
	}
	template('user/group-display');
}

if ($do == 'post') {
	uni_user_permission_check('system_user_group_post');
	$id = is_array($_GPC['id']) ? 0 : intval($_GPC['id']);
	$_W['page']['title'] = $id ? '编辑用户组 - 用户组 - 用户管理' : '添加用户组 - 用户组 - 用户管理';
	if (!empty($id)) {
		$group_info = pdo_fetch("SELECT * FROM ".tablename('users_group') . " WHERE id = :id", array(':id' => $id));
		$group_info['package'] = iunserializer($group_info['package']);
		if (!empty($group_info['package']) && in_array(-1, $group_info['package'])) $group_info['check_all'] = true;
	}
	$packages = uni_groups();
	foreach ($packages as $key => &$package_val) {
		if (!empty($group_info['package']) && in_array($key, $group_info['package'])) {
			$package_val['checked'] = true;
		} else {
			$package_val['checked'] = false;
		}
	}
	unset($package_val);
	if (checksubmit('submit')) {
		if (empty($_GPC['name'])) {
			itoast('请输入用户组名称！', '', '');
		}
		if (!empty($_GPC['package'])) {
			foreach ($_GPC['package'] as $value) {
				$package[] = intval($value);
			}
		}
		$data = array(
			'name' => $_GPC['name'],
			'package' => iserializer($package),
			'maxaccount' => intval($_GPC['maxaccount']),
			'maxwxapp' => intval($_GPC['maxwxapp']),
			'timelimit' => intval($_GPC['timelimit'])
		);
		if (empty($id)) {
			pdo_insert('users_group', $data);
		} else {
			pdo_update('users_group', $data, array('id' => $id));
		}
		itoast('用户组更新成功！', url('user/group/display'), 'success');
	}
	template('user/group-post');
}

if ($do == 'del') {
	uni_user_permission_check('system_user_group_del');
	$id = intval($_GPC['id']);
	$result = pdo_delete('users_group', array('id' => $id));
	if(!empty($result)){
		itoast('删除成功！', url('user/group/display'), 'success');
	}else {
		itoast('删除失败！请稍候重试！', url('user/group'), 'error');
	}
	exit;
}