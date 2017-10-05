<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('mc');
load()->model('platform');

$dos = array('display', 'delete', 'refresh', 'post', 'push', 'copy', 'current_menu');
$do = in_array($do, $dos) ? $do : 'display';
uni_user_permission_check('platform_menu');
$_W['page']['title'] = '公众号 - 自定义菜单';

if($_W['isajax']) {
	if(!empty($_GPC['method'])) {
		$do = $_GPC['method'];
	}
}

if($do == 'display') {
	$type = !empty($_GPC['type']) ? intval($_GPC['type']) : 1;
	set_time_limit(0);
	$account_api = WeAccount::create();
	$default_menu_info = $account_api->menuCurrentQuery();
	if (is_error($default_menu_info)) {
		itoast($default_menu_info['message'], '', 'error');
	}
	$default_menu = $default_menu_info['selfmenu_info'];
	$default_menu['type'] = 1;
	$default_menu['matchrule'] = array();
	if (!empty($default_menu['button'])) {
		foreach ($default_menu['button'] as $key=>&$button) {
			$default_sub_button[$key] = $button['sub_button'];
			ksort($button);
		}
		unset($button);
	}
	ksort($default_menu);
	$wechat_menu_data = base64_encode(iserializer($default_menu));
	$all_default_menus = pdo_getall('uni_account_menus', array('uniacid' => $_W['uniacid'], 'type' => 1), array('data', 'id'), 'id');
	foreach ($all_default_menus as $k=>$menu_data) {
		$single_menu_info = iunserializer(base64_decode($menu_data['data']));
		if (is_array($single_menu_info)) {
			$single_menu_info['type'] = 1;
			$single_menu_info['matchrule'] = array();
			if (!empty($single_menu_info['button'])) {
				foreach ($single_menu_info['button'] as $key=>&$single_button) {
					if (!empty($default_sub_button[$key])) {
						$single_button['sub_button'] = $default_sub_button[$key];
					} else {
						unset($single_button['sub_button']);
					}
					ksort($single_button);
				}
				unset($single_button);
				ksort($single_menu_info);
			}
			$local_menu_data = base64_encode(iserializer($single_menu_info));
			if ($wechat_menu_data == $local_menu_data) {
				$default_menu_id = $k;
			}
		}
	}

	if (!empty($default_menu_id)) {
		pdo_update('uni_account_menus', array('status' => 1), array('id' => $default_menu_id));
		pdo_update('uni_account_menus', array('status' => 0), array('uniacid' => $_W['uniacid'], 'type' => 1, 'id !=' => $default_menu_id));
	} else {
		$insert_data = array(
			'uniacid' => $_W['uniacid'],
			'type' => 1,
			'group_id' => -1,
			'sex' => 0,
			'data' => $wechat_menu_data,
			'client_platform_type' => 0,
			'area' => '',
			'menuid' => 0,
			'status' => 1
		);
		pdo_insert('uni_account_menus', $insert_data);
		$insert_id = pdo_insertid();
		pdo_update('uni_account_menus', array('title' => '默认菜单_'.$insert_id), array('id' => $insert_id));
		pdo_update('uni_account_menus', array('status' => 0), array('uniacid' => $_W['uniacid'], 'type' => 1, 'id !=' => $insert_id));
	}

		$get_menu_info = $account_api->menuQuery();
	if(is_error($get_menu_info)) {
		itoast($get_menu_info['message'], '', 'error');
	}
	$condition_menus = $get_menu_info['conditionalmenu'];
	pdo_update('uni_account_menus', array('status' => 0), array('uniacid' => $_W['uniacid'], 'type' => 3));
	if (!empty($condition_menus)) {
		foreach($condition_menus as $menu) {
			$data = array(
				'uniacid' => $_W['uniacid'],
				'type' => 3,
				'group_id' => isset($menu['matchrule']['tag_id']) ? $menu['matchrule']['tag_id'] : (isset($menu['matchrule']['group_id']) ? $menu['matchrule']['group_id'] : '-1'),
				'sex' => $menu['matchrule']['sex'],
				'client_platform_type' => $menu['matchrule']['client_platform_type'],
				'area' => trim($menu['matchrule']['country']) . trim($menu['matchrule']['province']) . trim($menu['matchrule']['city']),
				'data' => base64_encode(iserializer($menu)),
				'menuid' => $menu['menuid'],
				'status' => 1,
			);
			if (!empty($menu['matchrule'])) {
				$menu_id = pdo_get('uni_account_menus', array('uniacid' => $_W['uniacid'], 'menuid' => $menu['menuid'], 'type' => 3), array('id'));
			}
			if(!empty($menu_id['id'])) {
				$data['title'] = '个性化菜单_' . $menu_id['id'];
				pdo_update('uni_account_menus', $data, array('uniacid' => $_W['uniacid'], 'id' => $menu_id['id']));
			} else {
				pdo_insert('uni_account_menus', $data);
				$insert_id = pdo_insertid();
				pdo_update('uni_account_menus', array('title' => '个性化菜单_'.$insert_id), array('id' => $insert_id));
			}
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = " WHERE uniacid = :uniacid";
	$params[':uniacid'] = $_W['uniacid'];
	if (isset($_GPC['keyword'])) {
		$condition .= " AND title LIKE :keyword";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
	}
	if (!empty($type)) {
		$condition .= " AND type = :type";
		$params[':type'] = $type;
	}
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('uni_account_menus') . $condition, $params);
	$data = pdo_fetchall("SELECT * FROM " . tablename('uni_account_menus') . $condition . " ORDER BY type ASC, status DESC,id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	$names = array(
		'sex' => array(
			0 => '不限',
			1 => '男',
			2 => '女',
		),
		'client_platform_type' => array(
			0 => '不限',
			1 => '苹果',
			2 => '安卓',
			3 => '其他'
		),
	);
	$groups = mc_fans_groups(true);
	template('platform/menu');
}

if($do == 'push') {
	$id = intval($_GPC['id']);
	$data = pdo_get('uni_account_menus', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($data)) {
		iajax(-1, '菜单不存在或已删除', referer());
	}
	if ($_GPC['status'] == 1) {
		$post = iunserializer(base64_decode($data['data']));
		if(empty($post)) {
			iajax(-1, '菜单数据错误', referer());
		}
		$menu = array();
		if(!empty($post['button'])) {
			foreach($post['button'] as &$button) {
				$temp = array();
				$temp['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $button['name']);
				$temp['name'] = urlencode($temp['name']);
				if (empty($button['sub_button'])) {
					$temp['type'] = $button['type'];
					if($button['type'] == 'view') {
						$temp['url'] = urlencode($button['url']);
					} elseif ($button['type'] == 'media_id' || $button['type'] == 'view_limited') {
						$temp['media_id'] = urlencode($button['media_id']);
					} else {
						$temp['key'] = urlencode($button['key']);
					}
				} else {
					foreach($button['sub_button'] as &$subbutton) {
						$sub_temp = array();
						$sub_temp['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $subbutton['name']);
						$sub_temp['name'] = urlencode($sub_temp['name']);
						$sub_temp['type'] = $subbutton['type'];
						if($subbutton['type'] == 'view') {
							$sub_temp['url'] = urlencode($subbutton['url']);
						} elseif ($subbutton['type'] == 'media_id' || $subbutton['type'] == 'view_limited') {
							$sub_temp['media_id'] = urlencode($subbutton['media_id']);
						} else {
							$sub_temp['key'] = urlencode($subbutton['key']);
						}
						$temp['sub_button'][] = $sub_temp;
					}
					unset($subbutton);
				}
				$menu['button'][] = $temp;
			}
			unset($button);
		}

		if(!empty($post['matchrule'])) {
			if($post['matchrule']['sex'] > 0) {
				$menu['matchrule']['sex'] = $post['matchrule']['sex'];
			}
			if($post['matchrule']['group_id'] != -1) {
				$menu['matchrule']['tag_id'] = $post['matchrule']['group_id']; 						}
			if($post['matchrule']['client_platform_type'] > 0) {
				$menu['matchrule']['client_platform_type'] = $post['matchrule']['client_platform_type'];
			}
			if(!empty($post['matchrule']['province'])) {
				$menu['matchrule']['country'] = urlencode('中国');
				$menu['matchrule']['province'] = urlencode(rtrim($post['matchrule']['province'], '省'));
				if(!empty($post['matchrule']['city'])) {
					$menu['matchrule']['city'] = urlencode(rtrim($post['matchrule']['city'], '市'));
				}
			}
		}
		if ($data['type'] == 1) {
			unset($menu['matchrule']);
		}
		$account_api = WeAccount::create($_W['acid']);
		$result = $account_api->menuCreate($menu);
		if(is_error($result)) {
			iajax(-1, $result['message'], '');
		} else {
			if($data['type'] == 1) {
				pdo_update('uni_account_menus', array('status' => '1'), array('id' => $data['id']));
				pdo_update('uni_account_menus', array('status' => '0'), array('id !=' => $data['id'], 'uniacid' => $_W['uniacid'], 'type' => '1'));
			} elseif ($data['type'] == 3) {
								if($post['matchrule']['group_id'] != -1) {
					$menu['matchrule']['groupid'] = $menu['matchrule']['tag_id'];
					unset($menu['matchrule']['tag_id']);
				}
				$status = pdo_update('uni_account_menus', array('status' => 1, 'menuid' => $result), array('uniacid' => $_W['uniacid'], 'id' => $data['id']));
			}
			iajax(0, '推送成功', url('platform/menu/display', array('type' => $data['type'])));
		}
	} elseif ($_GPC['status'] == 2) {
		$status =  $_GPC['status'];
		if($data['type'] == 1 || ($data['type'] == 3 && $data['menuid'] > 0) && $status != 'history') {
			$account_api = WeAccount::create($_W['acid']);
			$result = $account_api->menuDelete($data['menuid']);
			if(is_error($result) && empty($_GPC['f'])) {
				$url = url('platform/menu/delete', array('id' => $id, 'f' => 1));
				$url_display = url('platform/menu/display', array('id' => $id, 'f' => 1));
				$message = "调用微信接口删除失败:{$result['message']}<br>";
				itoast(error(-1, $message), '', 'error');
			} else {
				pdo_update('uni_account_menus', array('status' => '0'), array('id' => $data['id']));
				iajax(0, '关闭成功', url('platform/menu/display', array('type' => $data['type'])));
			}
		}
	}
}

if($do == 'copy') {
	$id = intval($_GPC['id']);
	$menu = pdo_get('uni_account_menus', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($menu)) {
		itoast('菜单不存在或已经删除', url('platform/menu/display'), 'error');
	}
	if($menu['type'] != 3) {
		itoast('该菜单不能复制', url('platform/menu/display'), 'error');
	}
	unset($menu['id'], $menu['menuid']);
	$menu['status'] = 0;
	$menu['title'] = $menu['title'] . '- 复本';
	pdo_insert('uni_account_menus', $menu);
	$id = pdo_insertid();
	header('Location:' . url('platform/menu/post', array('id' => $id, 'copy' => 1)));
	die;
}

if($do == 'post') {
	$type = intval($_GPC['type']);
	$id = intval($_GPC['id']);
	$copy = intval($_GPC['copy']);
	$params = array();
	if($id > 0) {
		$menu = pdo_get('uni_account_menus', array('uniacid' => $_W['uniacid'], 'id' => $id));
		if(!empty($menu)) {
			$menu['data'] = iunserializer(base64_decode($menu['data']));
			if(!empty($menu['data'])) {
				if (!empty($menu['data']['button'])) {
					foreach ($menu['data']['button'] as &$button) {
						if (!empty($button['url'])) {
							$button['url'] = preg_replace('/(.*)redirect_uri=(.*)&response_type(.*)wechat_redirect/', '$2', $button['url']);
							$button['url'] = urldecode($button['url']);
						}
						if (empty($button['sub_button'])) {
							if ($button['type'] == 'media_id') {
								$button['type'] = 'click';
							}
							$button['sub_button'] = array();
						} else {
							$button['sub_button'] = !empty($button['sub_button']['list']) ? $button['sub_button']['list'] : $button['sub_button'];
							foreach($button['sub_button'] as &$subbutton) {
								if (!empty($subbutton['url'])) {
									$subbutton['url'] = preg_replace('/(.*)redirect_uri=(.*)&response_type(.*)wechat_redirect/', '$2', $subbutton['url']);
									$subbutton['url'] = urldecode($subbutton['url']);
								}
								if ($subbutton['type'] == 'media_id') {
									$subbutton['type'] = 'click';
								}
							}
							unset($subbutton);
						}
					}
					unset($button);
				}
				if(!empty($menu['data']['matchrule']['province'])) {
					$menu['data']['matchrule']['province'] .= '省';
				}
				if(!empty($menu['data']['matchrule']['city'])) {
					$menu['data']['matchrule']['city'] .= '市';
				}
				if (empty($menu['data']['matchrule']['sex'])) {
					$menu['data']['matchrule']['sex'] = 0;
				}
				if (empty($menu['data']['matchrule']['group_id'])) {
					$menu['data']['matchrule']['group_id'] = -1;
				}
				if (empty($menu['data']['matchrule']['client_platform_type'])) {
					$menu['data']['matchrule']['client_platform_type'] = 0;
				}
				if (empty($menu['data']['matchrule']['language'])) {
					$menu['data']['matchrule']['language'] = '';
				}
				$params = $menu['data'];
				$params['title'] = $menu['title'];
				$params['type'] = $menu['type'];
				$params['id'] = $menu['id'];
				$params['status'] = $menu['status'];
			}
			$type = $menu['type'];
		}
	}
	$status = $params['status'];
	$groups = mc_fans_groups();
	$languages = platform_menu_languages();
	if($_W['isajax'] && $_W['ispost']) {
		set_time_limit(0);
		$_GPC['group']['title'] = trim($_GPC['group']['title']);
		$_GPC['group']['type'] = intval($_GPC['group']['type']) == 0 ? 1 : intval($_GPC['group']['type']);
		$post = $_GPC['group'];
				if (empty($post['title'])) {
			iajax(-1, '请填写菜单组名称！', '');
		}
		$check_title_exist_condition = array(
			'title' => $post['title'],
			'type' => $type,
		);
		if (!empty($id)) {
			$check_title_exist_condition['id <>'] = $id;
		}
		$check_title_exist = pdo_getcolumn('uni_account_menus', $check_title_exist_condition, 'id');
		if (!empty($check_title_exist)) {
			iajax(-1, '菜单组名称已存在，请重新命名！', '');
		}
		$menu = array();
		if(!empty($post['button'])) {
			foreach($post['button'] as $key => &$button) {
				$temp = array();
				$temp['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $button['name']);
				$temp['name'] = urlencode($temp['name']);
				$keyword_exist = strexists($button['key'], 'keyword:');
				if ($keyword_exist) {
					$button['key'] = substr($button['key'], 8);
				}
				$state = 'we7sid-'.$_W['session_id'];

				if (empty($button['sub_button'])) {
					$temp['type'] = $button['type'];
					if($button['type'] == 'view') {
						$temp['url'] = urlencode($button['url']);
					} elseif ($button['type'] == 'click') {
						if (!empty($button['media_id']) && empty($button['key'])) {
							$temp['media_id'] = urlencode($button['media_id']);
							$temp['type'] = 'media_id';
						} elseif (empty($button['media_id']) && !empty($button['key'])) {
							$temp['type'] = 'click';
							$temp['key'] = urlencode($button['key']);
						}
					} elseif ($button['type'] == 'miniprogram') {
						$temp['type'] = 'miniprogram';
						$temp['appid'] = trim($button['appid']);
						$temp['pagepath'] = urlencode($button['pagepath']);
						$temp['url'] = urlencode($button['url']);
					} else {
						$temp['key'] = urlencode($button['key']);
					}
				} else {
					foreach($button['sub_button'] as &$subbutton) {
						$sub_temp = array();
						$sub_temp['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $subbutton['name']);
						$sub_temp['name'] = urlencode($sub_temp['name']);
						$sub_keyword_exist = strexists($subbutton['key'], 'keyword:');
						if ($sub_keyword_exist) {
							$subbutton['key'] = substr($subbutton['key'], 8);
						}
						$sub_temp['type'] = $subbutton['type'];
						if($subbutton['type'] == 'view') {
							$sub_temp['url'] = urlencode($subbutton['url']);
						} elseif ($subbutton['type'] == 'click') {
							if (!empty($subbutton['media_id']) && empty($subbutton['key'])) {
								$sub_temp['media_id'] = urlencode($subbutton['media_id']);
								$sub_temp['type'] = 'media_id';
							} elseif (empty($subbutton['media_id']) && !empty($subbutton['key'])) {
								$sub_temp['type'] = 'click';
								$sub_temp['key'] = urlencode($subbutton['key']);
							}
						} elseif ($subbutton['type'] == 'miniprogram') {
							$sub_temp['type'] = 'miniprogram';
							$sub_temp['appid'] = trim($subbutton['appid']);
							$sub_temp['pagepath'] = urlencode($subbutton['pagepath']);
							$sub_temp['url'] = urlencode($subbutton['url']);
						} else {
							$sub_temp['key'] = urlencode($subbutton['key']);
						}
						$temp['sub_button'][] = $sub_temp;
					}
					unset($subbutton);
				}
				$menu['button'][] = $temp;
			}
			unset($button);
		}

				if($post['type'] == 3 && empty($post['matchrule'])) {
			iajax(-1, '请选择菜单显示对象', '');
		}

		if($post['type'] == 3 && !empty($post['matchrule'])) {
			if($post['matchrule']['sex'] > 0) {
				$menu['matchrule']['sex'] = $post['matchrule']['sex'];
			}
			if($post['matchrule']['group_id'] != -1) {
				$menu['matchrule']['tag_id'] = $post['matchrule']['group_id'];					}
			if($post['matchrule']['client_platform_type'] > 0) {
				$menu['matchrule']['client_platform_type'] = $post['matchrule']['client_platform_type'];
			}

			if(!empty($post['matchrule']['province'])) {
				$menu['matchrule']['country'] = urlencode('中国');
				$menu['matchrule']['province'] = urlencode(str_replace('省', '', $post['matchrule']['province']));
				if(!empty($post['matchrule']['city'])) {
					$menu['matchrule']['city'] = urlencode(str_replace('市', '', $post['matchrule']['city']));
				}
			}
			if(!empty($post['matchrule']['language'])) {
				$inarray = 0;
				$languages = platform_menu_languages();
				foreach ($languages as $key => $value) {
					if(in_array($post['matchrule']['language'], $value, true)) $inarray = 1;
				}
				if($inarray === 1) $menu['matchrule']['language'] = $post['matchrule']['language'];
			}
		}
		$account_api = WeAccount::create();
		$result = $account_api->menuCreate($menu);
		if(is_error($result)) {
			iajax(1, $result);
		} else {
						if($post['matchrule']['group_id'] != -1) {
				$menu['matchrule']['groupid'] = $menu['matchrule']['tag_id'];
				unset($menu['matchrule']['tag_id']);
			}
			$menu = json_decode(urldecode(json_encode($menu)), true);
			if(!isset($menu['matchrule'])) {
				$menu['matchrule'] = array();
			}
			
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'menuid' => $result,
				'title' => $post['title'],
				'type' => $post['type'],
				'sex' => intval($menu['matchrule']['sex']),
				'group_id' => isset($menu['matchrule']['group_id']) ? $menu['matchrule']['group_id'] : -1,
				'client_platform_type' => intval($menu['matchrule']['client_platform_type']),
				'area' => trim($menus['matchrule']['country']) . trim($menu['matchrule']['province']) . trim($menu['matchrule']['city']),
				'data' => base64_encode(iserializer($menu)),
				'status' => 1,
				'createtime' => TIMESTAMP,
			);
			
			if($post['type'] == 1) {
				if (!empty($_GPC['id'])) {
					pdo_update('uni_account_menus', $insert, array('uniacid' => $_W['uniacid'], 'type' => 1, 'id' => intval($_GPC['id'])));
				} else {
					$default_menu_ids = pdo_getall('uni_account_menus', array('uniacid' => $_W['uniacid'], 'type' => 1, 'status' => 1), array('id'));
					foreach ($default_menu_ids as $id) {
						pdo_update('uni_account_menus', array('status' => '0'), array('id' => $id));
					}
					pdo_insert('uni_account_menus', $insert);
				}
				iajax(0, '创建菜单成功', url('platform/menu/display'));
			} elseif($post['type'] == 3) {
				if($post['status'] == 0 && $post['id'] > 0) {
					pdo_update('uni_account_menus', $insert, array('uniacid' => $_W['uniacid'], 'type' => 3, 'id' => $post['id']));
				} else {
					pdo_insert('uni_account_menus', $insert);
				}
				iajax(0, '创建菜单成功', url('platform/menu/display', array('type' => '3')));
			}
		}
	}
	template('platform/menu');
}

if($do == 'delete') {
	$id = intval($_GPC['id']);
	$data = pdo_get('uni_account_menus', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if(empty($data)) {
		itoast('菜单不存在或已经删除', referer(), 'error');
	}
	$status =  $_GPC['status'];

	if ($data['type'] == 3 && $data['status'] == 0) {
		pdo_delete('uni_account_menus', array('uniacid' => $_W['uniacid'], 'id' => $id));
		itoast('删除菜单成功', url('platform/menu/display', array('type' => $data['type'])), 'success');
	}
	if($data['type'] == 1 || ($data['type'] == 3 && $data['menuid'] > 0 && $data['status'] != 0)) {
		$account_api = WeAccount::create($_W['acid']);
		$result = $account_api->menuDelete($data['menuid']);
		if(is_error($result) && empty($_GPC['f'])) {
			if ($result['errno'] == '65301') {
				pdo_delete('uni_account_menus', array('uniacid' => $_W['uniacid'], 'id' => $id));
				itoast('删除菜单成功', referer(), 'success');
			}
			$url = url('platform/menu/delete', array('id' => $id, 'f' => 1));
			$url_display = url('platform/menu/display', array('id' => $id, 'f' => 1));
			$message = "调用微信接口删除失败:{$result['message']}<br>";
			itoast($message, '', 'error');
		}
	}
	if ($status == 'history') {
		if($data['type'] == 1) {
			pdo_delete('uni_account_menus', array('uniacid' => $_W['uniacid'], 'id' => $id, 'status' => '0'));
		} else {
			pdo_delete('uni_account_menus', array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
	} else {
		if($data['type'] == 1) {
			pdo_update('uni_account_menus', array('isdeleted' => 1), array('uniacid' => $_W['uniacid']));
		} else {
			pdo_update('uni_account_menus', array('isdeleted' => 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
	}
	itoast('删除菜单成功', url('platform/menu/display', array('type' => $data['type'])), 'success');
}

if ($do == 'current_menu') {
	$current_menu = $_GPC['current_menu'];
	if ($current_menu['type'] == 'click') {
		if (!empty($current_menu['media_id']) && empty($current_menu['key'])) {
			$wechat_attachment = pdo_get('wechat_attachment', array('media_id' => $current_menu['media_id']));
			if ($wechat_attachment['type'] == 'news') {
				$material = pdo_get('wechat_news', array('uniacid' => $_W['uniacid'], 'attach_id' => $wechat_attachment['id']));
				$material['items'][0]['thumb_url'] =  tomedia($material['thumb_url']);
				$material['items'][0]['title'] = $material['title'];
				$material['items'][0]['digest'] = $material['digest'];
				$material['type'] = 'news';
			} elseif ($wechat_attachment['type'] == 'video') {
				$material['tag'] = iunserializer($wechat_attachment['tag']);
				$material['attach'] = tomedia($wechat_attachment['attachment'], true);
				$material['type'] = 'video';
			} elseif ($wechat_attachment['type'] == 'voice') {
				$material['attach'] = tomedia($wechat_attachment['attachment'], true);
				$material['type'] = 'voice';
				$material['filename'] = $wechat_attachment['filename'];
			} elseif ($wechat_attachment['type'] == 'image') {
				$material['attach'] = tomedia($wechat_attachment['attachment'], true);
				$material['url'] = "url({$material['attach']})";
				$material['type'] = 'image';
			}
		} else {
			$keyword_info = explode(':', $current_menu['key']);
			if ($keyword_info[0] == 'keyword') {
				$rule_info = pdo_get('rule', array('name' => $keyword_info[1]), array('id'));
				$material['child_items'][0] = pdo_get('rule_keyword', array('rid' => $rule_info['id']), array('content'));
				$material['name'] = $keyword_info[1];
				$material['type'] = 'keyword';
			}
		}
	}
	if ($current_menu['type'] != 'click' && $current_menu['type'] != 'view') {
		$material = array();
		if ($current_menu['etype'] == 'module') {
			$module_name = explode(':', $current_menu['key']);
			load()->model('module');
			$material = module_fetch($module_name[1]);
			if($material['issystem']) {
				$path = '/framework/builtin/' . $material['name'];
			} else {
				$path = '../addons/' . $material['name'];
			}
			$cion = $path . '/icon-custom.jpg';
			if(!file_exists($cion)) {
				$cion = $path . '/icon.jpg';
				if(!file_exists($cion)) {
					$cion = './resource/images/nopic-small.jpg';
				}
			}
			$material['icon'] = $cion;
			$material['type'] = $current_menu['type'];
			$material['etype'] = 'module';
		} elseif ($current_menu['etype'] == 'click') {
			$keyword_info = explode(':', $current_menu['key']);
			if ($keyword_info[0] == 'keyword') {
				$rule_info = pdo_get('rule', array('name' => $keyword_info[1]), array('id'));
				$material['child_items'][0] = pdo_get('rule_keyword', array('rid' => $rule_info['id']), array('content'));
				$material['name'] = $keyword_info[1];
				$material['type'] = $current_menu['type'];
				$material['etype'] = 'click';
			}
		}
	}
	iajax(0, $material, '');
}
