<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->func('cron');
load()->model('cloud');
load()->model('module');

$dos = array('list', 'post', 'cron', 'send', 'del', 'preview');
$do = in_array($do, $dos) ? $do : 'list';
uni_user_permission_check('platform_mass_task');
$_W['page']['title'] = '定时群发-微信素材';

if ($do == 'list') {
	$time = strtotime(date('Y-m-d'));
	$record = pdo_getall('mc_mass_record', array('uniacid' => $_W['uniacid'], 'sendtime >=' => $time), array(), 'sendtime', 'sendtime ASC', array(1,7));

	$days = array();
	for ($i = 0; $i < 8; $i++) {
		$day_info = array();
		$day_info['day'] = date('Y-m-d', strtotime("+{$i} days", $time));

		$starttime = strtotime("+{$i} days", $time);
		$endtime = $i+1;
		$endtime = strtotime("+{$endtime} days", $time);
		$massdata = pdo_fetch('SELECT id, `groupname`, `msgtype`, `group`, `attach_id`, `media_id`, `sendtime` FROM '. tablename('mc_mass_record') . ' WHERE uniacid = :uniacid AND sendtime BETWEEN :starttime AND :endtime AND status = 1', array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime));

		if (!empty($massdata)) {
			$massdata['media'] = pdo_get('wechat_attachment', array('id' => $massdata['attach_id']));
			$massdata['media']['attach'] = tomedia($massdata['media']['attachment']);
			$massdata['media']['createtime_cn'] = date('Y-m-d H:i', $massdata['media']['createtime']);
			switch ($massdata['msgtype']) {
				case 'news':
					$massdata['msgtype_zh'] = '图文';
					$massdata['media']['items'] = pdo_getall('wechat_news', array('attach_id' => $massdata['attach_id']));
					foreach ($massdata['media']['items'] as  &$news_val) {
						$news_val['thumb_url'] = tomedia($news_val['thumb_url']);
					}
					unset($news_val);
					break;
				case 'image':
					$massdata['msgtype_zh'] = '图片';
					break;
				case 'voice':
					$massdata['msgtype_zh'] = '语音';
					break;
				case 'video':
					$massdata['msgtype_zh'] = '视频';
					$massdata['media']['attach']['tag'] = iunserializer($massdata['media']['tag']);
					break;
			}
			$massdata['clock'] = date('H:m', $massdata['sendtime']);
		}
		$day_info['info'] = $massdata;
		$days[] = $day_info;
	}
	
	template('platform/mass-display');
}

if ($do == 'del') {
	$mass = pdo_get('mc_mass_record', array('uniacid' => $_W['uniacid'], 'id' => intval($_GPC['id'])));
	if (!empty($mass) && $mass['cron_id'] > 0) {
		$status = cron_delete(array($mass['cron_id']));
		if (is_error($status)) {
			iajax(0, $status, '');
		}
	}
	pdo_delete('mc_mass_record', array('uniacid' => $_W['uniacid'], 'id' => intval($_GPC['id'])));
	iajax(0, '删除成功！', '');
}

if ($do == 'post') {
	$groups = pdo_get('mc_fans_groups', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid']));
	$groups = (array)iunserializer($groups['groups']);
		array_unshift($groups, array('id' => -1, 'name' => '全部粉丝', 'count' => ''));

	$time = strtotime(date('Y-m-d'));
	$starttime = strtotime("+{$_GPC['day']} days", $time);
	$endtime = $_GPC['day']+1;
	$endtime = strtotime("+{$endtime} days", $time);
	$massdata = pdo_fetch('SELECT id, `groupname`, `group`, `attach_id`, `media_id`, `sendtime` FROM '. tablename('mc_mass_record') . ' WHERE uniacid = :uniacid AND sendtime BETWEEN :starttime AND :endtime AND status = 1', array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime));
	if (!empty($massdata)) {
		$massdata['clock'] = date('H:i', $massdata['sendtime']);
	} else {
		$massdata['clock'] = '08:00';
	}

	if (checksubmit('submit')) {
		$cloud = cloud_prepare();
		if (is_error($cloud)) {
			iajax(0, $cloud, '');
		}
				$starttime = strtotime("+{$_GPC['day']} days", $time);
		$endtime = $_GPC['day']+1;
		$endtime = strtotime("+{$endtime} days", $time);
		set_time_limit(0);
		$records = pdo_fetchall('SELECT id, cron_id FROM ' . tablename('mc_mass_record') . ' WHERE uniacid = :uniacid AND sendtime BETWEEN :starttime AND :endtime AND status = 1 ORDER BY sendtime ASC LIMIT 8', array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime), 'id');
		if (!empty($records)) {
			foreach ($records as $record) {
				if (!$record['cron_id']) {
					continue;
				}
				$corn_ids[] = $record['cron_id'];
			}
			if (!empty($corn_ids)) {
				$status = cron_delete($corn_ids);
				if (is_error($status)) {
					itoast('删除群发错误,请重新提交', referer());
				}
			}
			$ids = implode(',', array_keys($records));
			pdo_query('DELETE FROM ' . tablename('mc_mass_record') . " WHERE uniacid = :uniacid AND id IN ({$ids})", array(':uniacid' => $_W['uniacid']));
		}
				$group = json_decode(htmlspecialchars_decode($_GPC['group']), true);
		$mass = array();
		if(!empty($_GPC['reply'])) {
			foreach ($_GPC['reply'] as $reply_k => $reply_val) {
				if (!empty($reply_val)) {
					$msgtype = substr($reply_k, 6);
					$mass['mediaid'] = trim($_GPC['reply']['reply_'.$msgtype]);
					$attachment = pdo_get('wechat_attachment', array('media_id' => $mass['mediaid']), array('id', 'model'));
					if ($attachment['model'] != 'perm') {
						itoast('图文素材请选择微信素材', '', 'info');
					}
					$mass['id'] = $attachment['id'];
					$mass['msgtype'] = $msgtype;
					break;
				}
			}
		}
		$time_key = date('Y-m-d', strtotime("+{$_GPC['day']} days", $time));
		$mass['sendtime'] = strtotime($time_key . " {$_GPC['clock']}");
		$cron_status = 0;
		$mass_record_data = array(
			'uniacid' => $_W['uniacid'],
			'acid' => $_W['acid'],
			'groupname' => $group['name'],
			'group' => $group['id'],
			'attach_id' => $mass['id'],
			'media_id' => $mass['mediaid'],
			'fansnum' => $group['count'],
			'msgtype' => $mass['msgtype'],
			'sendtime' => $mass['sendtime'],
			'createtime' => TIMESTAMP,
			'type' => 1,
			'status' => 1,
			'cron_id' => 0,
		);
		pdo_insert('mc_mass_record', $mass_record_data);
		$insert_id = pdo_insertid();
		$cron_data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $time_key . "微信群发任务",
			'filename' => 'mass',
			'type' => 1,
			'lastruntime' => $mass['sendtime'],
			'extra' => $insert_id,
			'module' => 'task',
			'status' => 1,
		);
		$status = cron_add($cron_data);
		if (is_error($status)) {
			$message = "{$time_key}的群发任务同步到云服务失败,请手动同步<br>";
			$cron_status = 1;
		} else {
			pdo_update('mc_mass_record', array('cron_id' => $status), array('id' => $insert_id));
		}
		if ($cron_status) {
			itoast($message, url('platform/mass/send'), 'info');
		}
		itoast('群发设置成功', url('platform/mass'), 'success');
	}

	template('platform/mass-post');
}

if ($do == 'cron') {
	$id = intval($_GPC['id']);
	$record = pdo_get('mc_mass_record', array('uniacid' => $_W['uniacid'], 'id' => $id));
	if (empty($record)) {
		itoast('群发任务不存在或已删除', referer(), 'error');
	}
	$cron = array(
		'uniacid' => $_W['uniacid'],
		'name' => date('Y-m-d', $record['sendtime']) . "微信群发任务",
		'filename' => 'mass',
		'type' => 1,
		'lastruntime' => $record['sendtime'],
		'extra' => $record['id'],
		'module' => 'task',
		'status' => 1
	);
	$status = cron_add($cron);
	if (is_error($status)) {
		itoast($status['message'], referer(), 'error');
	}
	pdo_update('mc_mass_record', array('cron_id' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	itoast('同步到云服务成功', referer(), 'success');
}

if ($do == 'preview') {
	$wxname = trim($_GPC['wxname']);
	if (empty($wxname)) {
		iajax(1, '微信号不能为空', '');
	}
	$type = trim($_GPC['type']);
	$media_id = trim($_GPC['media_id']);
	$account_api = WeAccount::create();
	$data = $account_api->fansSendPreview($wxname, $media_id, $type);
	if (is_error($data)) {
		iajax(-1, $data['message'], '');
	}
	iajax(0, 'success', '');
}

if ($do == 'send') {
	$_W['page']['title'] = '定时群发记录-定时群发';
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = ' WHERE `uniacid` = :uniacid AND `acid` = :acid';
	$params = array();
	$params[':uniacid'] = $_W['uniacid'];
	$params[':acid'] = $_W['acid'];
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('mc_mass_record') . $condition, $params);
	$lists = pdo_getall('mc_mass_record', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid']), array(), '', 'id DESC', 'LIMIT '.($pindex-1)* $psize.','.$psize);
	$types = array('text' => '文本消息', 'image' => '图片消息', 'voice' => '语音消息', 'video' => '视频消息', 'news' => '图文消息', 'wxcard' => '微信卡券');
	$pager = pagination($total, $pindex, $psize);
	template('platform/mass-send');
}