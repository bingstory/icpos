<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function user_register($user) {
	if (empty($user) || !is_array($user)) {
		return 0;
	}
	if (isset($user['uid'])) {
		unset($user['uid']);
	}
	$user['salt'] = random(8);
	$user['password'] = user_hash($user['password'], $user['salt']);
	$user['joinip'] = CLIENT_IP;
	$user['joindate'] = TIMESTAMP;
	$user['lastip'] = CLIENT_IP;
	$user['lastvisit'] = TIMESTAMP;
	if (empty($user['status'])) {
		$user['status'] = 2;
	}
	$result = pdo_insert('users', $user);
	if (!empty($result)) {
		$user['uid'] = pdo_insertid();
	}
	return intval($user['uid']);
}


function user_check($user) {
	if (empty($user) || !is_array($user)) {
		return false;
	}
	$where = ' WHERE 1 ';
	$params = array();
	if (!empty($user['uid'])) {
		$where .= ' AND `uid`=:uid';
		$params[':uid'] = intval($user['uid']);
	}
	if (!empty($user['username'])) {
		$where .= ' AND `username`=:username';
		$params[':username'] = $user['username'];
	}
	if (!empty($user['status'])) {
		$where .= " AND `status`=:status";
		$params[':status'] = intval($user['status']);
	}
	if (empty($params)) {
		return false;
	}
	$sql = 'SELECT `password`,`salt` FROM ' . tablename('users') . "$where LIMIT 1";
	$record = pdo_fetch($sql, $params);
	if (empty($record) || empty($record['password']) || empty($record['salt'])) {
		return false;
	}
	if (!empty($user['password'])) {
		$password = user_hash($user['password'], $record['salt']);
		return $password == $record['password'];
	}
	return true;
}


function user_single($user_or_uid) {
	$user = $user_or_uid;
	if (empty($user)) {
		return false;
	}
	if (is_numeric($user)) {
		$user = array('uid' => $user);
	}
	if (!is_array($user)) {
		return false;
	}
	$where = ' WHERE 1 ';
	$params = array();
	if (!empty($user['uid'])) {
		$where .= ' AND `uid`=:uid';
		$params[':uid'] = intval($user['uid']);
	}
	if (!empty($user['username'])) {
		$where .= ' AND `username`=:username';
		$params[':username'] = $user['username'];
	}
	if (!empty($user['email'])) {
		$where .= ' AND `email`=:email';
		$params[':email'] = $user['email'];
	}
	if (!empty($user['status'])) {
		$where .= " AND `status`=:status";
		$params[':status'] = intval($user['status']);
	}
	if (empty($params)) {
		return false;
	}
	$sql = 'SELECT * FROM ' . tablename('users') . " $where LIMIT 1";
	$record = pdo_fetch($sql, $params);
	if (empty($record)) {
		return false;
	}
	if (!empty($user['password'])) {
		$password = user_hash($user['password'], $record['salt']);
		if ($password != $record['password']) {
			return false;
		}
	}
	if($record['type'] == ACCOUNT_OPERATE_CLERK) {
		$clerk = pdo_get('activity_clerks', array('uid' => $record['uid']));
		if(!empty($clerk)) {
			$record['name'] = $clerk['name'];
			$record['clerk_id'] = $clerk['id'];
			$record['store_id'] = $clerk['storeid'];
			$record['store_name'] = pdo_fetchcolumn('SELECT business_name FROM ' . tablename('activity_stores') . ' WHERE id = :id', array(':id' => $clerk['storeid']));
			$record['clerk_type'] = '3';
			$record['uniacid'] = $clerk['uniacid'];
		}
	} else {
				$record['name'] = $record['username'];
		$record['clerk_id'] = $user['uid'];
		$record['store_id'] = 0;
		$record['clerk_type'] = '2';
	}
	return $record;
}


function user_update($user) {
	if (empty($user['uid']) || !is_array($user)) {
		return false;
	}
	$record = array();
	if (!empty($user['username'])) {
		$record['username'] = $user['username'];
	}
	if (!empty($user['password'])) {
		$record['password'] = user_hash($user['password'], $user['salt']);
	}
	if (!empty($user['lastvisit'])) {
		$record['lastvisit'] = (strlen($user['lastvisit']) == 10) ? $user['lastvisit'] : strtotime($user['lastvisit']);
	}
	if (!empty($user['lastip'])) {
		$record['lastip'] = $user['lastip'];
	}
	if (isset($user['joinip'])) {
		$record['joinip'] = $user['joinip'];
	}
	if (isset($user['remark'])) {
		$record['remark'] = $user['remark'];
	}
	if (isset($user['type'])) {
		$record['type'] = $user['type'];
	}
	if (isset($user['status'])) {
		$status = intval($user['status']);
		if (!in_array($status, array(1, 2))) {
			$status = 2;
		}
		$record['status'] = $status;
	}
	if (isset($user['groupid'])) {
		$record['groupid'] = $user['groupid'];
	}
	if (isset($user['starttime'])) {
		$record['starttime'] = $user['starttime'];
	}
	if (isset($user['endtime'])) {
		$record['endtime'] = $user['endtime'];
	}
	if(isset($user['lastuniacid'])) {
		$record['lastuniacid'] = intval($user['lastuniacid']);
	}
	if (empty($record)) {
		return false;
	}
	return pdo_update('users', $record, array('uid' => intval($user['uid'])));
}


function user_hash($passwordinput, $salt) {
	global $_W;
	$passwordinput = "{$passwordinput}-{$salt}-{$_W['config']['setting']['authkey']}";
	return sha1($passwordinput);
}


function user_level() {
	static $level = array(
		'-3' => '锁定用户',
		'-2' => '禁止访问',
		'-1' => '禁止发言',
		'0' => '普通会员',
		'1' => '管理员',
	);
	return $level;
}


function user_group_detail_info($groupid = 0) {
	$groupid = is_array($groupid) ? 0 : intval($groupid);
	if(empty($groupid)) {
		return false;
	}
	$group_info = array();
	$packages = uni_groups();
	$group_info = pdo_get('users_group', array('id' => $groupid));
	if(!empty($group_info)) {
		$group_info['package'] = (array)iunserializer($group_info['package']);
		foreach ($packages as $packages_key => $packages_val) {
			foreach ($group_info['package'] as $group_info_val) {
				if($group_info_val == -1) {
					$group_info['module_and_tpl'][-1] = array(
						'id' => '-1',
						'name' => '所有服务',
						'modules' => array('title' => '系统所有模块'),
						'templates' => array('title' => '系统所有模板'),
					);
					continue;
				}
				if($packages_key == $group_info_val) {
					$group_info['module_and_tpl'][] = array(
						'id' => $packages_val['id'],
						'name' => $packages_val['name'],
						'modules' => $packages_val['modules'],
						'wxapp' => $packages_val['wxapp'],
						'templates' => $packages_val['templates'],
					);
					continue;
				}
			}
		}
	}
	return $group_info;
}


function user_account_detail_info($uid) {
	$wxapps = $wechats = $account_lists = array();

	$sql = "SELECT b.uniacid, b.role, a.type FROM " . tablename('account'). " AS a LEFT JOIN ". tablename('uni_account_users') . " AS b ON a.uniacid = b.uniacid WHERE a.acid <> 0 AND a.isdeleted <> 1 AND b.uid = :uid";
	$account_users_info = pdo_fetchall($sql, array(':uid' => $uid), 'uniacid');
	foreach ($account_users_info as $uniacid => $account) {
		if ($account['type'] == ACCOUNT_TYPE_OFFCIAL_NORMAL || $account['type'] == ACCOUNT_TYPE_OFFCIAL_AUTH) {
			$app_user_info[$uniacid] = $account;
		} elseif ($account['type'] == ACCOUNT_TYPE_APP_NORMAL) {
			$wxapp_user_info[$uniacid] = $account;
		}
	}
	if (!empty($wxapp_user_info)) {
		$wxapps = pdo_fetchall("SELECT w.name, w.level, w.acid, a.* FROM " . tablename('uni_account') . " a INNER JOIN " . tablename(uni_account_tablename(ACCOUNT_TYPE_APP_NORMAL)) . " w USING(uniacid) WHERE a.uniacid IN (".implode(',', array_keys($wxapp_user_info)).") ORDER BY a.uniacid ASC", array(), 'acid');
	}
	if (!empty($app_user_info)) {
		$wechats = pdo_fetchall("SELECT w.name, w.level, w.acid, a.* FROM " . tablename('uni_account') . " a INNER JOIN " . tablename(uni_account_tablename(ACCOUNT_TYPE_OFFCIAL_NORMAL)) . " w USING(uniacid) WHERE a.uniacid IN (".implode(',', array_keys($app_user_info)).") ORDER BY a.uniacid ASC", array(), 'acid');
	}
	$accounts = array_merge($wxapps, $wechats);
	if (!empty($accounts)) {
		foreach ($accounts as &$account_val) {
			$account_val['thumb'] = tomedia('headimg_'.$account_val['acid']. '.jpg');
			foreach ($account_users_info as $uniacid => $user_info) {
				if ($account_val['uniacid'] == $uniacid) {
					$account_val['role'] = $user_info['role'];
					if ($user_info['type'] == ACCOUNT_TYPE_APP_NORMAL) {
						$account_lists['wxapp'][$uniacid] = $account_val;
					} elseif ($user_info['type'] == ACCOUNT_TYPE_OFFCIAL_NORMAL || $user_info['type'] == ACCOUNT_TYPE_OFFCIAL_AUTH) {
						$account_lists['wechat'][$uniacid] = $account_val;
					}
				}
			}
		}
		unset($account_val);
	}
	return $account_lists;
}



function user_modules($uid) {
	global $_W;
	load()->model('module');
	$cachekey = cache_system_key("user_modules:" . $uid);
	$modules = cache_load($cachekey);
	if (empty($modules)) {
		$founders = explode(',', $_W['config']['setting']['founder']);
		$user_info = user_single(array ('uid' => $uid));

		$system_modules = pdo_getall('modules', array('issystem' => 1), array('name'), 'name');
		if (empty($uid) || in_array($uid, $founders)) {
			$module_list = pdo_getall('modules', array(), array('name'), 'name', array('mid DESC'));
		} elseif (!empty($user_info) && empty($user_info['groupid'])) {
			$module_list = $system_modules;
		} else {
			$user_group_info = pdo_get('users_group', array ('id' => $user_info['groupid']));
			if (!empty($user_group_info) && !empty($user_group_info['package'])) {
				$packageids = unserialize($user_group_info['package']);
			} else {
				$packageids = array();
			}
						if (!empty($packageids) && in_array('-1', $packageids)) {
				$module_list = pdo_getall('modules', array(), array('name'), 'name', array('mid DESC'));
			} else {
								$package_group = pdo_getall('uni_group', array('id' => $packageids));
				if (!empty($package_group)) {
					foreach ($package_group as $row) {
						if (!empty($row['modules'])) {
							$row['modules'] = (array)unserialize($row['modules']);
						}
						$package_group_module = array();
						if (!empty($row['modules'])) {
							foreach ($row['modules'] as $modulename => $module) {
								if (!is_array($module)) {
									$modulename = $module;
								}
								$package_group_module[$modulename] = $modulename;
							}
						}
					}
				}
				$module_list = pdo_fetchall("SELECT name FROM ".tablename('modules')." WHERE
										name IN ('" . implode("','", $package_group_module) . "') OR issystem = '1' ORDER BY mid DESC", array(), 'name');
			}
		}
		$module_list = array_keys($module_list);
		$plugin_list = $modules = array();
		if (pdo_tableexists('modules_plugin')) {
			$plugin_list = pdo_getall('modules_plugin', array('name' => $module_list), array());
		}
		$have_plugin_module = array();
		if (!empty($plugin_list)) {
			foreach ($plugin_list as $plugin) {
				$have_plugin_module[$plugin['main_module']][$plugin['name']] = $plugin['name'];
				$module_key = array_search($plugin['name'], $module_list);
				if ($module_key !== false) {
					unset($module_list[$module_key]);
				}
			}
		}
		if (!empty($module_list)) {
			foreach ($module_list as $module) {
				$modules[] = $module;
				if (!empty($have_plugin_module[$module])) {
					foreach ($have_plugin_module[$module] as $plugin) {
						$modules[] = $plugin;
					}
				}
			}
		}
		cache_write($cachekey, $modules);
	}

	$module_list = array();
	if (!empty($modules)) {
		foreach ($modules as $module) {
			$module_info = module_fetch($module);
			if (!empty($module_info)) {
				$module_list[$module] = $module_info;
			}
		}
	}
	return $module_list;
}


function user_login_forward($forward = '') {
	global $_W;
	$login_forward = trim($forward);
	
	if (!empty($forward)) {
		return $login_forward;
	}
	if (!empty($_W['isfounder'])) {
		return url('account/manage');
	}
	
	$login_forward = url('account/display');
	if (!empty($_W['uniacid']) && !empty($_W['account'])) {
		if ($_W['account']['type'] == ACCOUNT_TYPE_OFFCIAL_NORMAL || $_W['account']['type'] == ACCOUNT_TYPE_OFFCIAL_AUTH) {
			$login_forward = url('home/welcome');
		} elseif ($_W['account']['type'] == ACCOUNT_TYPE_APP_NORMAL) {
			$login_forward = url('wxapp/display/home');
		}
	}
	
	return $login_forward;
}
