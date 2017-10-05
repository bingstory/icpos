<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

$we7_system_menu = array();
$we7_system_menu['account'] = array(
	'title' => '公众号',
	'url' => url('home/welcome'),
	'section' => array(
	
	'renren' => array(
			'title' => '人人店',
			'menu' => array(
				'mc_fans' => array(
					'title' => '人人店',
					'url' => './index.php?c=site&a=entry&m=ewei_shopv2&do=web',
					'icon' => 'wi wi-fansmanage',
					'permission_name' => 'mc_fans',
				)
			),
		),
	
	'platform_module' => array(
			'title' => '应用模块',
			'menu' => array(),
			'is_display' => true,
		),
	
	
		'platform_plus' => array(
			'title' => '增强功能',
			'menu' => array(
				'platform_reply' => array(
					'title' => '自动回复',
					'url' => url('platform/reply'),
					'icon' => 'wi wi-reply',
					'permission_name' => 'platform_reply',
					'sub_permission' => array(
						array(
							'title' => '关键字自动回复 ',
							'permission_name' => 'platform_reply',
						),
						array(
							'title' => '非关键字自动回复 ',
							'permission_name' => 'platform_reply_special',
						),
						array(
							'title' => '欢迎/默认回复',
							'permission_name' => 'platform_reply_system',
						),
					),
				),
				'platform_menu' => array(
					'title' => '自定义菜单',
					'url' => url('platform/menu'),
					'icon' => 'wi wi-custommenu',
					'permission_name' => 'platform_menu',
				),
				'platform_qr' => array(
					'title' => '二维码/转化链接',
					'url' => url('platform/qr'),
					'icon' => 'wi wi-qrcode',
					'permission_name' => 'platform_qr',
					'sub_permission' => array(
						array(
							'title' => '二维码',
							'permission_name' => 'platform_qr',
						),
						array(
							'title' => '转化链接',
							'permission_name' => 'platform_url2qr',
						),
					),
				),
				'platform_mass_task' => array(
					'title' => '定时群发',
					'url' => url('platform/mass'),
					'icon' => 'wi wi-crontab',
					'permission_name' => 'platform_mass_task',
				),
				'platform_material' => array(
					'title' => '素材/编辑器',
					'url' => url('platform/material'),
					'icon' => 'wi wi-redact',
					'permission_name' => 'platform_material',
				),
				'platform_site' => array(
					'title' => '微官网-文章',
					'url' => url('site/multi/display'),
					'icon' => 'wi wi-home',
					'permission_name' => 'platform_site',
					'sub_permission' => array(
						array(
							'title' => '添加/编辑',
							'permission_name' => 'platform_site_post',
						),
						array(
							'title' => '删除',
							'permission_name' => 'platform_site_delete',
						),
					),
				)
			),
		),
		
		'mc' => array(
			'title' => '粉丝',
			'menu' => array(
				'mc_fans' => array(
					'title' => '粉丝管理',
					'url' => url('mc/fans'),
					'icon' => 'wi wi-fansmanage',
					'permission_name' => 'mc_fans',
				),
				'mc_member' => array(
					'title' => '会员管理',
					'url' => url('mc/member'),
					'icon' => 'wi wi-fans',
					'permission_name' => 'mc_member',
				)
			),
		),
		'profile' => array(
			'title' => '配置',
			'menu' => array(
				'profile' => array(
					'title' => '参数配置',
					'url' => url('profile/passport'),
					'icon' => 'wi wi-parameter-stting',
					'permission_name' => 'profile_setting',
				)
			),
		),
	),
);
$we7_system_menu['wxapp'] = array(
	'title' => '小程序',
	'url' => url('wxapp/display/home'),
	'section' => array(
		'wxapp_module' => array(
			'title' => '应用',
			'menu' => array(),
			'is_display' => true,
		),
		'platform_manage_menu' => array(
			'title' => '管理',
			'menu' => array(
				'module_link' => array(
					'title' => "模块关联公众号",
					'url' => url('wxapp/version/module_link_uniacid'),
					'is_display' => 1,
					'icon' => 'wi wi-appsetting',
					'permission_name' => 'wxapp_module_link_uniacid',
				),
				'wxapp_profile' => array(
					'title' => '支付参数',
					'url' => url('wxapp/payment'),
					'is_display' => 1,
					'icon' => 'wi wi-appsetting',
					'permission_name' => 'wxapp_payment',
				)
			)
		)
	),
);

$we7_system_menu['system'] = array(
	'title' => '系统管理',
	'url' => url('account/manage', array('account_type' => '1')),
	'section' => array(
		'wxplatform' => array(
			'title' => '公众号',
			'menu' => array(
				'system_account' => array(
					'title' => ' 微信公众号',
					'url' => url('account/manage', array('account_type' => '1')),
					'icon' => 'wi wi-wechat',
					'permission_name' => 'system_account',
					'sub_permission' => array(
						array(
							'title' => '公众号管理设置',
							'permission_name' => 'system_account_manage',
						),
						array(
							'title' => '添加公众号',
							'permission_name' => 'system_account_post',
						),
						array(
							'title' => '公众号停用',
							'permission_name' => 'system_account_stop',
						),
						array(
							'title' => '公众号回收站',
							'permission_name' => 'system_account_recycle',
						),
						array(
							'title' => '公众号删除',
							'permission_name' => 'system_account_delete',
						),
						array(
							'title' => '公众号恢复',
							'permission_name' => 'system_account_recover',
						),
					),
				),
				'system_module' => array(
					'title' => '应用管理',
					'url' => url('system/module', array('account_type' => '1')),
					'icon' => 'wi wi-wx-apply',
					'permission_name' => 'system_module',
				),
				'system_template' => array(
					'title' => '微官网模板',
					'url' => url('system/template'),
					'icon' => 'wi wi-wx-template',
					'permission_name' => 'system_template',
				),
				'system_platform' => array(
					'title' => ' 微信开放平台',
					'url' => url('system/platform'),
					'icon' => 'wi wi-exploitsetting',
					'permission_name' => 'system_platform',
				),
			)
		),
		'module' => array(
			'title' => '小程序',
			'menu' => array(
				'system_wxapp' => array(
					'title' => '微信小程序',
					'url' => url('account/manage', array('account_type' => '4')),
					'icon' => 'wi wi-wxapp',
					'permission_name' => 'system_wxapp',
				),
				'system_module_wxapp' => array(
					'title' => '小程序应用',
					'url' => url('system/module', array('account_type' => '4')),
					'icon' => 'wi wi-wxapp-apply',
					'permission_name' => 'system_module_wxapp',
				),
			)
		),
		'user' => array(
			'title' => '帐户/用户',
			'menu' => array(
				'system_my' => array(
					'title' => '我的帐户',
					'url' => url('user/profile'),
					'icon' => 'wi wi-user',
					'permission_name' => 'system_my',
				),
				'system_user' => array(
					'title' => '用户管理',
					'url' => url('user/display'),
					'icon' => 'wi wi-user-group',
					'permission_name' => 'system_user',
					'sub_permission' => array(
							array(
								'title' => '编辑用户',
								'permission_name' => 'system_user_post',
							),
							array(
								'title' => '审核用户',
								'permission_name' => 'system_user_check',
							),
							array(
								'title' => '用户回收站',
								'permission_name' => 'system_user_recycle',
							),
							array(
								'title' => '用户属性设置',
								'permission_name' => 'system_user_fields',
							),
							array(
								'title' => '用户属性设置-编辑字段',
								'permission_name' => 'system_user_fields_post',
							),
							array(
								'title' => '用户注册设置',
								'permission_name' => 'system_user_registerset',
							),
					),
				),
			)
		),
		'permission' => array(
			'title' => '权限管理',
			'menu' => array(
				'system_module_group' => array(
					'title' => '应用权限组',
					'url' => url('system/module-group'),
					'icon' => 'wi wi-appjurisdiction',
					'permission_name' => 'system_module_group',
				),
				'system_user_group' => array(
					'title' => '用户权限组',
					'url' => url('user/group'),
					'icon' => 'wi wi-userjurisdiction',
					'permission_name' => 'system_user_group',
					'sub_permission' => array(
						array(
							'title' => '编辑用户组',
							'permission_name' => 'system_user_group_post',
						),
						array(
							'title' => '删除用户组',
							'permission_name' => 'system_user_group_del',
						),
					),
				),
			)
		),
		'acticle' => array(
			'title' => '文章/公告',
			'menu' => array(
				'system_article' => array(
					'title' => '文章管理',
					'url' => url('article/news'),
					'icon' => 'wi wi-article',
					'permission_name' => 'system_article_news',
				),
				'system_article_notice' => array(
					'title' => '公告管理',
					'url' => url('article/notice'),
					'icon' => 'wi wi-notice',
					'permission_name' => 'system_article_notice',
				)
			)
		),
		'cache' => array(
			'title' => '缓存',
			'menu' => array(
				'system_setting_updatecache' => array(
					'title' => '更新缓存',
					'url' => url('system/updatecache'),
					'icon' => 'wi wi-update',
					'permission_name' => 'system_setting_updatecache',
				),
			),
		),
	),
);

$we7_system_menu['site'] = array(
	'title' => '站点管理',
	'url' => url('cloud/upgrade'),
	'section' => array(
		
		'setting' => array(
			'title' => '设置',
			'menu' => array(
				'system_setting_site' => array(
					'title' => '站点设置',
					'url' => url('system/site'),
					'icon' => 'wi wi-site-setting',
					'permission_name' => 'system_setting_site',
				),
				'system_setting_menu' => array(
					'title' => '菜单设置',
					'url' => url('system/menu'),
					'icon' => 'wi wi-menu-setting',
					'permission_name' => 'system_setting_menu',
				),
				'system_setting_attachment' => array(
					'title' => '附件设置',
					'url' => url('system/attachment'),
					'icon' => 'wi wi-attachment',
					'permission_name' => 'system_setting_attachment',
				),
				'system_setting_systeminfo' => array(
					'title' => '系统信息',
					'url' => url('system/systeminfo'),
					'icon' => 'wi wi-system-info',
					'permission_name' => 'system_setting_systeminfo',
				),
				'system_setting_logs' => array(
					'title' => '查看日志',
					'url' => url('system/logs'),
					'icon' => 'wi wi-log',
					'permission_name' => 'system_setting_logs',
				),
			)
		),
		'utility' => array(
			'title' => '常用工具',
			'menu' => array(
				
				'system_utility_database' => array(
					'title' => '数据库',
					'url' => url('system/database'),
					'icon' => 'wi wi-sql',
					'permission_name' => 'system_utility_database',
				),
				
			)
		),
	),
	'founder' => true,
);

/* $we7_system_menu['adviertisement'] = array (
	'title' => '广告联盟',
	'url' => url('adviertisement/content-provider'),
	'section' => array(
		'adviertisement' => array(
			'title' => '常用系统工具',
			'menu' => array(
				'adviertisement-content-provider' => array(
					'title' => '流量主',
					'url' => url('adviertisement/content-provider/account_list'),
					'icon' => 'wi wi-flow',
					'permission_name' => 'adviertisement_content-use',
				),
				'adviertisement-content-create' => array(
					'title' => '广告主',
					'url' => url('adviertisement/content-provider/content_provider'),
					'icon' => 'wi wi-adgroup',
					'permission_name' => 'adviertisement_content-create',
				),
			)
		),
	),
	'founder' => true,
); */




return $we7_system_menu;