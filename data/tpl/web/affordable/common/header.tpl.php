<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header-base', TEMPLATE_INCLUDEPATH)) : (include template('common/header-base', TEMPLATE_INCLUDEPATH));?>
<div data-skin="default" class="skin-default <?php  if($_GPC['main-lg']) { ?> main-lg-body <?php  } ?>">
<?php  $frames = buildframes(FRAME);_calc_current_frames($frames);?>
<div class="head">
	<nav class="navbar navbar-default" role="navigation">
		<div class="container <?php  if(!empty($frames['section']['platform_module_menu']['plugin_menu'])) { ?>plugin-head<?php  } ?>">
			<div class="navbar-header">
				<a class="navbar-brand" href="<?php  echo $_W['siteroot'];?>">
					<img src="<?php  if(!empty($_W['setting']['copyright']['blogo'])) { ?><?php  echo tomedia($_W['setting']['copyright']['blogo'])?><?php  } else { ?>./resource/affordable/images/logo/logo.png<?php  } ?>" class="pull-left" width="110px" height="35px">
					
				</a>
			</div>
			<?php  if(!empty($_W['uid'])) { ?>
			<div class="collapse navbar-collapse">
				<ul   class="nav navbar-nav navbar-left">
				
				
				
															<li ><a style="color:#00B58A;"  href="./index.php?c=account&a=display&" >公众号列表</a></li>
															
															
															   <li><a  style="color:#00B58A;" href="./index.php?c=site&a=entry&m=ewei_shopv2&do=web" target="_blank" >人人店分销</a></li>
															
															<li><a style="color:#00B58A;"   href="./index.php?c=account&a=manage&account_type=1" >系统设置</a></li>
															
															<li><a  style="color:#00B58A;" href="./index.php?c=user&a=profile&" >用户管理</a></li>
															
															<li><a  style="color:#00B58A;" href="./index.php?c=wxapp&a=display&do=home&" >小程序</a></li>
															

										
										<li><a  style="color:#00B58A;" href="./index.php?c=system&a=site&" >站点管理</a></li>
										
										
									</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="wi wi-user color-gray"></i><?php  echo $_W['user']['username'];?> <span class="caret"></span></a>
						<ul class="dropdown-menu color-gray" role="menu">
							<li>
								<a href="<?php  echo url('user/profile');?>" target="_blank"><i class="wi wi-account color-gray"></i> 我的账号</a>
							</li>
							<?php  if($_W['isfounder']) { ?>
							<li class="divider"></li>
							<li><a href="<?php  echo url('cloud/upgrade');?>" target="_blank"><i class="wi wi-update color-gray"></i> 自动更新</a></li>
							<li><a href="<?php  echo url('system/updatecache');?>" target="_blank"><i class="wi wi-cache color-gray"></i> 更新缓存</a></li>
							<li class="divider"></li>
							<?php  } ?>
							<li>
								<a href="<?php  echo url('user/logout');?>"><i class="fa fa-sign-out color-gray"></i> 退出系统</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<?php  } else { ?>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown"><a href="<?php  echo url('user/register');?>">注册</a></li>
					<li class="dropdown"><a href="<?php  echo url('user/login');?>">登陆</a></li>
				</ul>
			</div>
			<?php  } ?>
		</div>
	</nav>
</div>
<?php  if(empty($_COOKIE['check_setmeal']) && !empty($_W['account']['endtime']) && ($_W['account']['endtime'] - TIMESTAMP < (6*86400))) { ?> 
<div class="system-tips we7-body-alert" id="setmeal-tips">
	<div class="container text-right">
		<div class="alert-info">
			<a href="<?php  if($_W['isfounder']) { ?><?php  echo url('user/edit', array('uid' => $_W['account']['uid']));?><?php  } else { ?>javascript:void(0);<?php  } ?>">
				您的服务有效期限：<?php  echo date('Y-m-d', $_W['account']['starttime']);?> ~ <?php  echo date('Y-m-d', $_W['account']['endtime']);?>.
				<?php  if($_W['account']['endtime'] < TIMESTAMP) { ?>
				目前已到期，请联系管理员续费
				<?php  } else { ?>
				将在<?php  echo floor(($_W['account']['endtime'] - strtotime(date('Y-m-d')))/86400);?>天后到期，请及时付费
				<?php  } ?>
			</a>
			<span class="tips-close" onclick="check_setmeal_hide();"><i class="wi wi-error-sign"></i></span>
		</div>
	</div>
</div>
<script>
	function check_setmeal_hide() {
		util.cookie.set('check_setmeal', 1, 1800);
		$('#setmeal-tips').hide();
		return false;
	}
</script>
<?php  } ?> 
<div class="main">
<?php  if(!defined('IN_MESSAGE')) { ?>
<div class="container">
	<?php  if(in_array(FRAME, array('account', 'system', 'adviertisement', 'wxapp', 'site')) && !in_array($_GPC['a'], array('news-show', 'notice-show'))) { ?>
		<a href="javascript:;" class="js-big-main button-to-big color-gray" title="加宽"><?php  if($_GPC['main-lg']) { ?>正常<?php  } else { ?>宽屏<?php  } ?></a>
	<div class="panel panel-content main-panel-content <?php  if(!empty($frames['section']['platform_module_menu']['plugin_menu'])) { ?>panel-content-plugin<?php  } ?>">
		<div class="content-head panel-heading main-panel-heading">
			<?php  if(($_GPC['c'] != 'cloud' && !empty($_GPC['m']) && !in_array($_GPC['m'], array('keyword', 'special', 'welcome', 'default', 'userapi', 'service'))) || defined('IN_MODULE')) { ?>
				<a href="<?php  echo url('home/welcome/account')?>" class="we7-head-back"><i class="wi wi-back-circle"></i></a>
				<span class="we7-head-account"><a href="<?php  echo url('home/welcome/account')?>"><?php  echo $_W['account']['name'];?></a></span>
				<?php  if(file_exists(IA_ROOT. "/addons/". $_W['current_module']['name']. "/icon-custom.jpg")) { ?>
				<img src="<?php  echo tomedia("addons/".$_W['current_module']['name']."/icon-custom.jpg")?>" class="head-app-logo" onerror="this.src='./resource/affordable/images/gw-wx.gif'">
				<?php  } else { ?>
				<img src="<?php  echo tomedia("addons/".$_W['current_module']['name']."/icon.jpg")?>" class="head-app-logo" onerror="this.src='./resource/affordable/images/gw-wx.gif'">
				<?php  } ?>
				<span class="font-lg"><?php  echo $_W['current_module']['title'];?></span>

				<!-- 兼容历史性问题：模块内获取不到模块信息$module的问题-start -->
				<?php  if(CRUMBS_NAV == 1) { ?>
				<?php  global $module;?>
				<?php  } ?>
				<!-- end -->
			<?php  } else if(FRAME == 'account') { ?>
				<img src="<?php  echo tomedia('headimg_'.$_W['account']['acid'].'.jpg')?>?time=<?php  echo time()?>" class="head-logo">
				<span class="font-lg"><?php  echo $_W['account']['name'];?></span>

				<?php  if($_W['account']['level'] == 1 || $_W['account']['level'] == 3) { ?>
					<span class="label label-primary">订阅号</span><?php  if($_W['account']['level'] == 3) { ?><span class="label label-primary">已认证</span><?php  } ?>
				<?php  } ?>
				<?php  if($_W['account']['level'] == 2 || $_W['account']['level'] == 4) { ?>
					<span class="label label-primary">服务号</span> <?php  if($_W['account']['level'] == 4) { ?><span class="label label-primary">已认证</span><?php  } ?>
				<?php  } ?>
				<?php  if($_W['uniaccount']['isconnect'] == 0) { ?>
					<span class="tips-danger">
						<i class="wi wi-warning-sign"></i>未接入微信公众号
						<a href="<?php  echo url('account/post', array('uniacid' => $_W['account']['uniacid'], 'acid' => $_W['acid']))?>">立即接入</a>
					</span>
					<?php  } ?>
					<span class="pull-right"><a href="<?php  echo url('account/display')?>" class="color-default we7-margin-left"><i class="wi wi-cut color-default"></i>切换公众号</a></span>
				<?php  if(uni_permission($_W['uid'], $_W['uniacid']) != ACCOUNT_MANAGE_NAME_OPERATOR) { ?>
					<span class="pull-right"><a href="<?php  echo url('account/post', array('uniacid' => $_W['account']['uniacid'], 'acid' => $_W['acid']))?>"><i class="wi wi-appsetting"></i>公众号设置</a></span>
				<?php  } ?>
				<span class="pull-right"><a href="<?php  echo url('utility/emulator');?>" target="_blank"><i class="wi wi-iphone"></i>模拟测试</a></span>
			<?php  } ?>
			<?php  if(FRAME == 'system') { ?>
				<span class="font-lg"><i class="wi wi-setting"></i> 系统管理</span>
			<?php  } ?>
			<?php  if(FRAME == 'site') { ?>
				<span class="font-lg"><i class="wi wi-system-site"></i> 站点管理</span>
			<?php  } ?>
			<?php  if(FRAME == 'adviertisement') { ?>
				<span class="font-lg"><i class="wi wi-ad"></i>广告联盟</span>
			<?php  } ?>
			<?php  if(FRAME == 'wxapp') { ?>
				<span class="wxapp-name"><?php  echo $wxapp_info['name'];?></span>
				<span class="wxapp-version"><?php  echo $version_info['version'];?></span>
				<div class="pull-right">
					<a href="<?php  echo url('wxapp/version/display', array('uniacid' => $version_info['uniacid']))?>" class="color-default"><i class="wi wi-cut"></i>切换版本</a>
					<?php  if(in_array($role, array(ACCOUNT_MANAGE_NAME_OWNER, ACCOUNT_MANAGE_NAME_MANAGER)) || $_W['isfounder']) { ?>
					<a href="<?php  echo url('wxapp/manage/display', array('uniacid' => $version_info['uniacid']))?>" class="color-default"><i class="wi wi-text"></i>管理</a>
					<?php  } ?>
					<a href="<?php  echo url('wxapp/display')?>" class="color-default"><i class="wi wi-small-routine"></i>切换小程序</a>
				</div>
			<?php  } ?>
		</div>
	<div class="panel-body clearfix main-panel-body <?php  if(!empty($_W['setting']['copyright']['leftmenufixed'])) { ?>menu-fixed<?php  } ?>">
		<div class="left-menu">
			<div class="left-menu-content">
			<?php  if(empty($frames['section']['platform_module_menu']['plugin_menu'])) { ?>
				<?php  if(is_array($frames['section'])) { foreach($frames['section'] as $frame_section_id => $frame_section) { ?>
				<?php  if(!isset($frame_section['is_display']) || !empty($frame_section['is_display'])) { ?>
				<div class="panel panel-menu">
					<?php  if($frame_section['title']) { ?>
					<div class="panel-heading">
						<span class="no-collapse"><?php  echo $frame_section['title'];?><i class="wi wi-appsetting pull-right setting"></i></span>
					</div>
					<?php  } ?>
					<ul class="list-group">
						<?php  if(is_array($frame_section['menu'])) { foreach($frame_section['menu'] as $menu_id => $menu) { ?>
						<?php  if(!empty($menu['is_display'])) { ?>
							<?php  if($menu_id == 'platform_module_more') { ?>
							<li class="list-group-item list-group-more">
								<a href="<?php  echo url('profile/module');?>"><span class="label label-more">更多应用</span></a>
							</li>
							<?php  } else { ?>
							<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
								<a href="<?php  echo $menu['url'];?>" class="text-over" <?php  if($frame_section_id == 'platform_module') { ?>target="_blank"<?php  } ?>>
								<?php  if($menu['icon']) { ?>
								<?php  if($frame_section_id == 'platform_module') { ?>
								<img src="<?php  echo $menu['icon'];?>"/>
								<?php  } else { ?>
								<i class="<?php  echo $menu['icon'];?>"></i>
								<?php  } ?>
								<?php  } ?>
								<?php  echo $menu['title'];?>
								</a>
							</li>
							<?php  } ?>
						<?php  } ?>
						<?php  } } ?>
					</ul>
				</div>
				<?php  } ?>
				<?php  } } ?>
			<?php  } else { ?>
				<div class="plugin-menu clearfix">
					<div class="plugin-menu-main pull-left">
						<ul class="list-group">
							<li class="list-group-item<?php  if($_GPC['m'] == '') { ?> active<?php  } ?>">
								<a href="<?php  echo url('home/welcome/ext', array('m' => $frames['section']['platform_module_menu']['plugin_menu']['main_module']))?>">
									<i class="wi wi-main-apply"></i>
									<div>主应用</div>
								</a>
							</li>
							<li class="list-group-item">
								<div>插件</div>
							</li>
							<?php  if(is_array($frames['section']['platform_module_menu']['plugin_menu']['menu'])) { foreach($frames['section']['platform_module_menu']['plugin_menu']['menu'] as $plugin_name => $plugin) { ?>
							<li class="list-group-item<?php  if($_GPC['m'] == $plugin_name) { ?> active<?php  } ?>">
								<a href="<?php  echo url('home/welcome/ext', array('m' => $plugin_name))?>">
									<img src="<?php  echo $plugin['icon'];?>" alt="" class="img-icon" />
									<div><?php  echo $plugin['title'];?></div>
								</a>
							</li>
							<?php  } } ?>
						</ul>
						<?php  unset($plugin_name);?>
						<?php  unset($plugin);?>
					</div>
					<div class="plugin-menu-sub pull-left">
						<?php  if(is_array($frames['section'])) { foreach($frames['section'] as $frame_section_id => $frame_section) { ?>
						<?php  if(!isset($frame_section['is_display']) || !empty($frame_section['is_display'])) { ?>
							<div class="panel panel-menu">
								<?php  if($frame_section['title']) { ?>
								<div class="panel-heading">
									<span class="no-collapse"><?php  echo $frame_section['title'];?><i class="wi wi-appsetting pull-right setting"></i></span>
								</div>
								<?php  } ?>
								<ul class="list-group panel-collapse">
									<?php  if(is_array($frame_section['menu'])) { foreach($frame_section['menu'] as $menu_id => $menu) { ?>
									<?php  if(!empty($menu['is_display'])) { ?>
									<?php  if($menu_id == 'platform_module_more') { ?>
									<li class="list-group-item list-group-more">
										<a href="<?php  echo url('profile/module');?>"><span class="label label-more">更多应用</span></a>
									</li>
									<?php  } else { ?>
									<li class="list-group-item <?php  if($menu['active']) { ?>active<?php  } ?>">
										<a href="<?php  echo $menu['url'];?>" class="text-over" <?php  if($frame_section_id == 'platform_module') { ?>target="_blank"<?php  } ?>>
										<?php  if($menu['icon']) { ?>
											<?php  if($frame_section_id == 'platform_module') { ?>
											<img src="<?php  echo $menu['icon'];?>"/>
											<?php  } else { ?>
											<i class="<?php  echo $menu['icon'];?>"></i>
											<?php  } ?>
										<?php  } ?>
										<?php  echo $menu['title'];?>
										</a>
									</li>
									<?php  } ?>
									<?php  } ?>
									<?php  } } ?>
								</ul>
							</div>
						<?php  } ?>
						<?php  } } ?>
					</div>
				</div>
			<?php  } ?>
			</div>
		</div>
		<div class="right-content">
	<?php  } ?>
<?php  } ?>
