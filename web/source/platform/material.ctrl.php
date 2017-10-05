<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');
load()->model('material');
load()->model('mc');
load()->func('file');

$dos = array('display', 'sync', 'delete', 'send');
$do = in_array($do, $dos) ? $do : 'display';

uni_user_permission_check('platform_material');

$_W['page']['title'] = '永久素材-微信素材';

if ($do == 'send') {
	$group = intval($_GPC['group']);
	$type = trim($_GPC['type']);
	$id = intval($_GPC['id']);
	$media = pdo_get('wechat_attachment', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if (empty($media)) {
		iajax(1, '素材不存在', '');
	}
	$group = $group > 0 ? $group : -1;
	$account_api = WeAccount::create();
	$result = $account_api->fansSendAll($group, $type, $media['media_id']);
	if (is_error($result)) {
		iajax(1, $result['message'], '');
	}
	$groups = pdo_get('mc_fans_groups', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid']));
	if (!empty($groups)) {
		$groups = iunserializer($groups['groups']);
	}
	if ($group == -1) {
		$groups = array(
				$group => array(
						'name' => '全部粉丝',
						'count' => 0
				)
		);
	}
	$record = array(
		'uniacid' => $_W['uniacid'],
		'acid' => $_W['acid'],
		'groupname' => $groups[$group]['name'],
		'fansnum' => $groups[$group]['count'],
		'msgtype' => $type,
		'group' => $group,
		'attach_id' => $id,
		'media_id' => $media['media_id'],
		'status' => 0,
		'type' => 0,
		'sendtime' => TIMESTAMP,
		'createtime' => TIMESTAMP,
	);
	pdo_insert('mc_mass_record', $record);
	iajax(0, '发送成功！', '');
}

if ($do == 'display') {
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'news';
	$server = $_GPC['server'] == 'local' ? 'local' : 'wechat';
	$upload_limit = material_upload_limit();
	$group = mc_fans_groups(true);
	$pageindex = max(1, intval($_GPC['page']));
	$pagesize = 24;
	$search = addslashes($_GPC['title']);
	$material_list = $conditions = array();
	$tables = array('local' => 'core_attachment', 'wechat' => 'wechat_attachment');
	if ($type == 'news') {
		$conditions[':uniacid'] = $_W['uniacid'];
		$search_sql = '';
		if (! empty($search)) {
			$search_sql = " AND (b.title LIKE :search_title OR b.author = :search_author OR b.digest LIKE :search_digest)";
			$conditions[':search_title'] = "%{$search}%";
			$conditions[':search_author'] = "%{$search}%";
			$conditions[':search_digest'] = "%{$search}%";
		}

		$select_sql = "SELECT  %s FROM " . tablename('wechat_attachment') . " AS a RIGHT JOIN " . tablename('wechat_news') . " AS b ON a.id = b.attach_id WHERE a.uniacid = :uniacid AND a.type = 'news' AND a.id <> ''" . $search_sql . "%s";

		$list_sql = sprintf($select_sql, "*, a.id as id", " ORDER BY a.createtime DESC, b.displayorder ASC LIMIT " . ($pageindex - 1) * $pagesize . ", " . $pagesize);
		$total_sql = sprintf($select_sql, "count(*)", '');

		$total = pdo_fetchcolumn($total_sql, $conditions);
		$news_list = pdo_fetchall($list_sql, $conditions);
		if (! empty($news_list)) {
			foreach ($news_list as $news){
				if (isset($material_list[$news['attach_id']])){
					$material_list[$news['attach_id']]['items'][$news['displayorder']] = $news;
				}else{
					$material_list[$news['attach_id']] = array(
						'id' => $news['id'],
						'filename' => $news['filename'],
						'attachment' => $news['attachment'],
						'media_id' => $news['media_id'],
						'type' => $news['type'],
						'model' => $news['model'],
						'tag' => $news['tag'],
						'createtime' => $news['createtime'],
						'items' => array($news['displayorder'] => $news),
					);
				}
			}
		}
		unset($news_list);
		$pager = pagination($total, $pageindex, $pagesize);
	} else {
		$conditions['uniacid'] = $_W['uniacid'];
		$table = $tables[$server];
		switch ($type) {
			case 'image' :
				$conditions['type'] = $server == 'local' ? ATTACH_TYPE_IMAGE : 'image';
				break;
			case 'voice' :
				$conditions['type'] = $server == 'local' ? ATTACH_TYPE_VOICE : 'voice';
				break;
			case 'video' :
				$conditions['type'] = $server == 'local' ? ATTACH_TYPE_VEDIO : 'video';
				break;
			default :
				$conditions['type'] = $server == 'local' ? ATTACH_TYPE_IMAGE : 'image';
				break;
		}
		if ($server == 'local') {
			$material_list = pdo_getslice($table, $conditions, array($pageindex, $pagesize), $total, array(), '', 'createtime DESC');
		} else {
			$conditions['model'] = 'perm';
			$material_list = pdo_getslice($table, $conditions, array($pageindex, $pagesize), $total, array(), '', 'createtime DESC');
			if ($type == 'video'){
				foreach ($material_list as &$row) {
					$row['tag'] = $row['tag'] == '' ? array() : iunserializer($row['tag']);
				}
				unset($row);
			}
		}
		$pager = pagination($total, $pageindex, $pagesize);
	}
}

if ($do == 'delete') {
	$material_id = intval($_GPC['material_id']);
	$server = $_GPC['server'] == 'local' ? 'local' : 'wechat';
	$type = trim($_GPC['type']);
	$cron_record = pdo_get('mc_mass_record', array('uniacid' => $_W['uniacid'], 'attach_id' => $material_id), array('id'));
	if (!empty($cron_record)) {
		iajax('-1', '有群发消息未发送，不可删除');
	}
	if ($type == 'news'){
		$result = material_news_delete($material_id);
	} else {
				$result = material_delete($material_id, $server);
	}
	if (is_error($result)){
		iajax('-1', $result['message']);
	}
	iajax('0', '删除素材成功');
}

if ($do == 'sync') {
	$account_api = WeAccount::create($_W['acid']);
	$pageindex = max(1, $_GPC['pageindex']);
	$type = empty($_GPC['type']) ? 'news' : $_GPC['type'];
	$news_list = $account_api->batchGetMaterial($type, ($pageindex - 1) * 20);
	$wechat_existid = empty($_GPC['wechat_existid']) ? array() : $_GPC['wechat_existid'];
	if ($pageindex == 1) {
		$original_newsid = pdo_getall('wechat_attachment', array('uniacid' => $_W['uniacid'], 'type' => $type, 'model' => 'perm'), array('id'), 'id');
		$original_newsid = array_keys($original_newsid);
		$wechat_existid = material_sync($news_list['item'], array(), $type);
		if ($news_list['total_count'] > 20) {
			$total = ceil($news_list['total_count']/20);
			iajax('1', array('type' => $type,'total' => $total, 'pageindex' => $pageindex+1, 'wechat_existid' => $wechat_existid, 'original_newsid' => $original_newsid), '');
		}
	} else {
		$wechat_existid = material_sync($news_list['item'], $wechat_existid, $type);
		$total = intval($_GPC['total']);
		$original_newsid = $_GPC['original_newsid'];
		if ($total != $pageindex) {
			iajax('1', array('type' => $type, 'total' => $total, 'pageindex' => $pageindex+1, 'wechat_existid' => $wechat_existid, 'original_newsid' => $original_newsid), '');
		}
		if (empty($original_newsid)) {
			$original_newsid = array();
		}
	}
	$delete_id = array_diff($original_newsid, $wechat_existid);
	if (!empty($delete_id) && is_array($delete_id)) {
		foreach ($delete_id as $id) {
			pdo_delete('wechat_attachment', array('uniacid' => $_W['uniacid'], 'id' => $id));
			pdo_delete('wechat_news', array('uniacid' => $_W['uniacid'], 'attach_id' => $id));
		}
	}
	iajax(0, '更新成功！', '');
}

template('platform/material');