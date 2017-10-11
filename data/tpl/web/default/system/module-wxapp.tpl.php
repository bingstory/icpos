<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  if($do == 'installed') { ?>
<div class="we7-page-title">
	小程序管理
</div>
<ul class="we7-page-tab">
	<li class="active"><a href="<?php  echo url('system/module/installed', array('account_type' => 4))?>">已安装的小程序  </a></li>
	<li><a href="<?php  echo url('system/module/not_installed', array('account_type' => 4))?>" ng-if="isFounder == 1">未安装的小程序<span class="color-red">  (<?php  echo $total_uninstalled;?>) </span></a></li>
	<li><a href="<?php  echo url('system/module/not_installed', array('account_type' => 4, 'status' => 'recycle'))?>" ng-if="isFounder == 1">已停用小程序</a></li>
</ul>
<div id="js-system-module" ng-controller="installedCtrl" ng-cloak>
	<div class="we7-page-search clearfix">
		<!--<div class="pull-right">-->
		<!--<a href="添加.html" class="btn btn-danger">购买应用模块</a>-->
		<!--</div>-->
		<form action="" method="get" class="row">
			<div class="form-group we7-margin-bottom  col-sm-4">
				<input type="hidden" name="letter" ng-model="activeLetter">
				<input type="hidden" name="c" value="system">
				<input type="hidden" name="a" value="module">
				<input type="hidden" name="do" value="page">
				<input type="hidden" name="account_type" value="4">
				<div class="input-group">
					<input class="form-control" name="title" value="<?php  echo $title;?>" type="text" placeholder="名称" >
					<span class="input-group-btn"><button class="btn btn-default" id="search"><i class="fa fa-search"></i></button></span>
				</div>
			</div>
		</form>
	</div>
	<div class="clearfix"></div>

	<ul class="letters-list">
		<li ng-class="activeLetter == letter ? 'active' : ''" ng-repeat="letter in letters"><a href="javascript:;" ng-click="searchLetter(letter)">{{ letter }}</a></li>
	</ul>

	<form action="" method="get">
		<table class="table we7-table table-hover vertical-middle table-manage">
			<col width="120px" />
			<col width="350px"/>
			<col width="230px" />
			<tr>
				<th colspan="2" class="text-left">小程序应用</th>
				<!--<th>公众号</th>-->
				<!--<th>数量</th>-->
				<th class="text-right">操作</th>
			</tr>
			<tr ng-repeat="module in module_list">
				<td class="text-left">
					<img ng-src="{{ module.logo }}" class="img-responsive icon"/>
				</td>
				<td class="text-left">
					<p>{{ module.title }}</p>
					<span>版本：{{ module.version }} </span><span class="color-red" ng-if="module.upgrade && isFounder == 1">发现新版本</span>
				</td>
				<!--<td >{{ module.use_account }}</td>-->
				<!--<td >{{ module.enabled_use_account }}</td>-->
				<td class="text-left">
					<div class="link-group">
						<a ng-href="{{ './index.php?c=system&a=module&do=upgrade&module_name='+module.name}}&account_type=<?php echo ACCOUNT_TYPE;?>" class="color-red" ng-if="module.upgrade && module.from != 'cloud' && isFounder == 1">升级</a>
						<a href="javascript:;" class="del color-red" ng-click="setUpgradeInfo(module.name)" ng-if="module.upgrade && module.from == 'cloud' && isFounder == 1">升级</a>
						<a href="<?php  echo url('system/module/module_detail')?>&name={{ module.name }}">管理设置</a>
						<!--<a href="javascript:;" ng-if="isFounder == 1" ng-click="editModule(module.mid)">编辑</a>-->
						<a href="<?php  echo url('system/module/uninstall', array('account_type' => 4))?>&name={{ module.name }}&account_type=<?php echo ACCOUNT_TYPE;?>" ng-if="isFounder == 1" class="del">停用</a>
					</div>
				</td>
			</tr>
		</table>
		<div class="select-all">
			<div class="we7-form text-right">
				<?php  echo $pager;?>
			</div>
		</div>
	</form>

	<div class="modal fade" id="module-info"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog we7-modal-dialog" style="width:800px">
			<div class="modal-content">
				<form action="" method="post" enctype="multipart/form-data" class="form-horizontal form" id="form-info">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title">编辑模块信息</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="col-sm-2 control-label"> 模块标题</label>
							<div class="col-sm-10">
								<input type="text" name="title" ng-model="moduleinfo.title" class="form-control">
								<span class="help-block">模块的名称, 显示在用户的模块列表中. 不要超过10个字符</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"> 模块简述</label>
							<div class="col-sm-10">
								<input type="text" name="ability" ng-model="moduleinfo.ability" class="form-control">
								<span class="help-block">模块功能描述, 使用简单的语言描述模块的作用, 来吸引用户</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"> 模块介绍</label>
							<div class="col-sm-10">
								<textarea type="text" name="description" ng-model="moduleinfo.description" class="form-control" rows="5">{{ moduleinfo.description }}</textarea>
								<span class="help-block">模块详细描述, 详细介绍模块的功能和使用方法</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"> 模块缩略图</label>
							<div class="col-sm-10">
								<div class="we7-input-img" ng-class="{ 'active' : moduleinfo.logo }" style="width: 100px;height: 100px; font-size: 45px;">
									<img ng-src="{{ moduleinfo.logo }}" ng-if="moduleinfo.logo">
									<a href="javascript:;" class="input-addon" ng-hide="moduleinfo.logo" ng-click="changePicture('logo')"><span>+</span></a>
									<input type="hidden" name="thumb">
									<div class="cover-dark">
										<a href="javascript:;" class="cut" ng-click="changePicture('logo')">更换</a>
										<a href="javascript:;" class="del" ng-click="delPicture('logo')"><i class="fa fa-times text-danger"></i></a>
									</div>
								</div>
								<span class="help-block">用 48*48 的图片来让你的模块更吸引眼球吧。仅支持jpg格式</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label"> 模块封面</label>
							<div class="col-sm-10">
								<div class="we7-input-img" ng-class="{ 'active' : moduleinfo.logo}"  style="width: 100px;height: 100px; font-size: 45px;">
									<img ng-src="{{ moduleinfo.preview }}">
									<a href="javascript:;" class="input-addon" ng-click="changePicture('preview')"><span>+</span></a>
									<input type="hidden" name="thumb">
									<div class="cover-dark">
										<a href="javascript:;" class="cut" ng-click="changePicture('preview')">更换</a>
										<a href="javascript:;" class="del" ng-click="delPicture('preview')"><i class="fa fa-times text-danger"></i></a>
									</div>
								</div>
								<span class="help-block">模块封面, 大小为 600*350, 更好的设计将会获得官方推荐位置。仅支持jpg格式</span>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
						<button  class="btn btn-primary" type="text" name="submit" ng-click="save()" data-dismiss="modal">保存</button>
						<input type="hidden" name="token" value="c781f0df">
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="upgrade-info"  tabindex="-1" role="dialog" aria-labelledby="myModalLabels" aria-hidden="true">
		<div class="modal-dialog we7-modal-dialog" style="width:800px">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">模块分支版本信息</h4>
				</div>
				<div class="modal-body">
					<div style="margin:-30px -30px 30px;" class="modal-alert">
						<div class="alert alert-info">
							<p><i class="wi  wi-info-sign"></i> 应用分支按照等级顺序排列。</p>
							<p><i class="wi  wi-info-sign"></i> 如果要升级到其它分支最新版本，需要花费对应分支价格数量的交易币。</p>
							<p><i class="wi  wi-info-sign"></i> 已购买的模块分支可以免费升级到该分支的最新版本。</p>
						</div>
					</div>
					<table class="table we7-table vertical-middle">
						<col width="">
						<col width="180px">
						<col width="400px">
						<tr>
							<th colspan="3" class="text-left">{{ module_list[upgradeInfo.name].title }}---模块分支信息</th>
						</tr>
						<tr>
							<td class="text-left">
								分支名称
							</td>
							<td class="text-center">
								升级价格
							</td>
							<td class="text-center">
								操作
							</td>
						</tr>
						<tr ng-repeat="branch in upgradeInfo.branches">
							<td class="text-left">  {{ branch.name }}</td>
							<td class="text-center">  {{  branch.id > upgradeInfo.site_branch.id ? branch.upgrade_price : 0 }}元</td>
							
							<td class="text-right">
								<div class="link-group">
									<a tabindex="2" href="javascript:;" role="button" data-toggle="popover" title="{{ module_list[upgradeInfo.name].title }}升级说明" data-container="#upgrade-info" data-placement="bottom" data-trigger="focus" data-html="true" data-content="{{ branch.version.description }}">升级说明</a>
									<a ng-href="{{ './index.php?c=cloud&a=process&m='+upgradeInfo.name+'&is_upgrade=1' }}" onclick="return confirm('确定要升级到此分之的最新版吗？')" ng-if="branch.id == upgradeInfo.site_branch.id">免费升级到【{{branch.name}}】最新版本</a>
									<a ng-href="{{ './index.php?c=cloud&a=redirect&do=buybranch&m='+upgradeInfo.name+'&branch='+branch.id+'&is_upgrade=1' }}" ng-click="upgrade(branch.upgrade_price)" ng-if="branch.id > upgradeInfo.site_branch.id">付费升级到【{{branch.name}}】最新版本</a>
								</div>
							</td>
							<script>
								$('[data-toggle="popover"]').popover();
							</script>
						</tr>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	require(['fileUploader'], function() {
		angular.module('moduleApp').value('config', {
			'isFounder' : '<?php  if($_W['isfounder']) { ?>1<?php  } else { ?>2<?php  } ?>',
			'letters': <?php  echo json_encode($letters)?>,
			'module_list': <?php  echo json_encode($module_list)?>,
			'editModuleUrl': "<?php  echo url('system/module/get_module_info')?>",
			'saveModuleUrl' :  "<?php  echo url('system/module/save_module_info')?>",
			'checkUpgradeUrl' : "<?php  echo url('system/module/check_upgrade')?>",
			'get_upgrade_info_url' : "<?php  echo url('system/module/get_upgrade_info')?>"
		});
		angular.bootstrap($('#js-system-module'), ['moduleApp']);
	});
</script>
<?php  } else if($do == 'not_installed') { ?>
<div class="we7-page-title">
	应用管理
</div>
<ul class="we7-page-tab">
	<li><a href="<?php  echo url('system/module/installed', array('account_type' => 4))?>">已安装应用  </a></li>
	<li <?php  if($status == 'uninstalled') { ?>class="active"<?php  } ?>><a href="<?php  echo url('system/module/not_installed', array('account_type' => 4))?>">未安装的应用<span class="color-red">  (<?php  echo $total_uninstalled;?>) </span></a></li>
	<li <?php  if($status == 'recycle') { ?>class="active"<?php  } ?>><a href="<?php  echo url('system/module/not_installed', array('account_type' => 4, 'status' => 'recycle'))?>">已停用应用</a></li>
</ul>
<div id="js-system-module-not_installed" ng-controller="notInstalledCtrl" ng-cloak>
	<div class="we7-page-search clearfix">
		<!--<div class="pull-right">-->
		<!--<a href="添加.html" class="btn btn-danger">购买应用模块</a>-->
		<!--</div>-->
		<form action="" method="get" class="row">
			<div class="form-group col-sm-4">
				<div class="input-group we7-margin-bottom">
					<input type="hidden" name="c" value="system">
					<input type="hidden" name="a" value="module">
					<input type="hidden" name="do" value="not_installed">
					<input type="hidden" name="status" value="<?php  if($status == 'recycle') { ?>recycle<?php  } else { ?>uninstalled<?php  } ?>">
					<input type="hidden" name="account_type" value="4">
					<input type="hidden" name="letter" value="<?php  echo $letter;?>">
					<input class="form-control" name="title" value="<?php  echo $title;?>" type="text" placeholder="名称" >
					<span class="input-group-btn"><button id="search" class="btn btn-default"><i class="fa fa-search"></i></button></span>
				</div>
			</div>
		</form>
	</div>
	<div class="clearfix">	</div>

	<ul class="letters-list">
		<li ng-repeat="letter in letters"><a href="javascript:;" ng-click="searchLetter(letter)">{{ letter }}</a></li>
	</ul>

	<table class="table we7-table table-hover vertical-middle table-manage">
		<tr>
			<th colspan="2" class="text-left">小程序应用</th>
			<th class="text-left">操作</th>
		</tr>
		<tr ng-repeat="module in module_list">
			<td class="text-left">
				<img ng-src="{{ module.logo }}" class="img-responsive" style="width: 50px;height: 50px;"/>
			</td>
			<td class="text-left">
				<p>{{ module.title }}</p>
				<span>版本：{{ module.version }} </span>
			</td>
			<td class="text-left">
				<a href="<?php  echo url('system/module/upgrade', array('account_type' => 4))?>&module_name={{ module.name }}" ng-if="module.upgrade_support == true" class="btn btn-primary">安装应用模块</a>
				<a href="<?php  echo url('system/module/install', array('account_type' => 4))?>&module_name={{ module.name }}" ng-if="module.upgrade_support != true" class="btn btn-primary">安装应用模块</a>
			</td>
		</tr>
	</table>
	</form>
	<div class="text-right">
		<?php  echo $pager;?>
	</div>
</div>
<script>
	angular.module('moduleApp').value('config', {
		'letters' : <?php  echo json_encode($letters)?>,
		'module_list' : <?php  echo json_encode($uninstallModules)?>
	});
	angular.bootstrap($('#js-system-module-not_installed'), ['moduleApp']);
</script>
<?php  } else if($do == 'module_detail') { ?>
<div class="js-system-module-detail" ng-controller="detailCtrl" ng-cloak>
	<ol class="breadcrumb we7-breadcrumb">
		<a href="<?php  echo referer()?>"><i class="wi wi-back-circle"></i> </a>
		<li>
			应用列表
		</li>
		<li>
			应用管理
		</li>
	</ol>
	<div class="module-info media">
		<a class="pull-left" href="#">
			<img src="<?php  echo tomedia($module_info['logo'])?>" style="width: 80px;height: 80px;" class="info-logo">
		</a>
		<div class="pull-right info-edit">
			<a href="" class="data color-gray hidden">到期时间 :2016/12/26 </a>
			<a href="#" class="btn btn-primary" data-toggle="modal" ng-if="isFounder == 1" ng-click="editModule('<?php  echo $module_info['mid'];?>')">编 辑</a>
			<a href="" class="btn btn-danger" ng-if="isFounder == 1">升级</a>
		</div>
		<div class="media-body">
			<h4 class="media-heading"><?php  echo $module_info['title'];?></h4>
		</div>
	</div>
	<div class="module-description">
		<dl class="description">
			<dt><i class="wi wi-appsetting"></i>功能介绍 </dt>
			<dd>
				<?php  echo $module_info['description'];?>
			</dd>
		</dl>
		<dl class="copyright" ng-if="isFounder == 1">
			<dt><i class="wi wi-appsetting"></i>版权信息  </dt>
			<dd>
				<span>名称：<?php  echo $module_info['title'];?></span>  <span>版本：<?php  echo $module_info['version'];?></span>  <span> 作者：<?php  echo $module_info['author'];?></span>
			</dd>
			<!--<dd>-->
			<!--<span>QQ:361635464</span>  <span>微信：3513854324</span>-->
			<!--</dd>-->
		</dl>
	</div>
	<div class="module-group" ng-if="isFounder == 1">
		<table class="table we7-table table-hover">
			<col />
			<col width="115px" />
			<tr>
				<th class="text-left">
					应用权限组
				</th>
				<th>
					<a href="<?php  echo url('system/module-group')?>" class="color-default">添加</a>
				</th>
			</tr>
			<tr>
				<td class="text-left">
					<span>所有服务</span>
				</td>
				<td>
				</td>
			</tr>
			<?php  if(is_array($module_group)) { foreach($module_group as $group) { ?>
			<tr>
				<td class="text-left">
					<span><?php  echo $group['name'];?></span>
				</td>
				<td>
					<div class="link-group"><a href="<?php  echo url('system/module-group/post', array('id' => $group['id']))?>">设置</a></div>
				</td>
			</tr>
			<?php  } } ?>
		</table>
	</div>
	<?php  if(!empty($module_subscribes)) { ?>
	<div class="panel we7-panel module-subscription" ng-if="isFounder == 1">
		<div class="panel-heading ">
			订阅详情
			<div class="pull-right subscription-switch">
				<span >禁用/开启</span>
				<label>
					<input name="" id="" class="form-control" type="checkbox"  style="display: none;">
					<div class="switch" ng-class="{ 'switchOn' : receive_ban == 1}" ng-click="changeSwitch()"></div>
				</label>
			</div>
		</div>
		<div class="panel-body">
			<ul>
				<?php  if(is_array($module_subscribes)) { foreach($module_subscribes as $subscribe) { ?>
				<li><?php  echo $mtypes[$subscribe];?> <?php  if($check_subscribe == 0) { ?><lable class="label label-danger">通讯失败</lable><?php  } ?>  </li>
				<?php  } } ?>
			</ul>
		</div>
	</div>
	<?php  } ?>
	<table class="table we7-table table-hover" ng-if="isFounder == 1">
		<col width="255px"/>
		<col width="130px"/>
		<col width="250px"/>
		<col width="122px"/>
		<col />
		<!--<tr>-->
			<!--<th class="text-left">-->
				<!--使用公众号-->
			<!--</th>-->
			<!--<th>-->
				<!--类型-->
			<!--</th>-->
			<!--<th>-->

			<!--</th>-->
			<!--<th>-->
			<!--</th>-->
			<!--<th class="text-left">-->
				<!--操作-->
			<!--</th>-->
		<!--</tr>-->
		<?php  if(is_array($use_module_account)) { foreach($use_module_account as $account) { ?>
		<!--<tr>-->
			<!--<td class="text-left">-->
				<?php  if(empty($account['name'])) { ?>
				<!--未知公众号-->
				<?php  } else { ?>
				<?php  echo $account['name'];?>
				<?php  } ?>
			<!--</td>-->
			<!--<td>-->
				<!--<span class="color-gray">-->
					<?php  if($account['level'] == 1) { ?>
					<!--普通订阅号-->
					<?php  } else if($account['level'] == 2) { ?>
					<!--普通服务号-->
					<?php  } else if($account['level'] == 3) { ?>
					<!--认证订阅号-->
					<?php  } else if($account['level'] == 4) { ?>
					<!--认证服务号-->
					<?php  } ?>
				<!--</span>-->
			<!--</td>-->
			<!--<td>-->
				<!--<span class="color-gray">     </span>-->
			<!--</td>-->
			<!--<td>-->
				<!--<span class="color-green"></span>-->
			<!--</td>-->
			<!--<td class="text-left">-->
				<!--<a href="<?php  echo url('account/post/module_tpl', array('uniacid' => $account['uniacid'], 'acid' => $account['acid']))?>" class="color-default">公众号权限设置</a>-->
			<!--</td>-->
		<!--</tr>-->
		<?php  } } ?>
	</table>
	<!--<div ng-if="isFounder == 1" class="text-right">-->
		<?php  echo $pager;?>
	<!--</div>-->
	<div class="modal fade" id="module-info"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog we7-modal-dialog" style="width:800px">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">编辑模块信息</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="col-sm-2 control-label"> 模块标题</label>
						<div class="col-sm-10">
							<input type="text" name="title" ng-model="moduleinfo.title" class="form-control">
							<span class="help-block">模块的名称, 显示在用户的模块列表中. 不要超过10个字符</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"> 模块简述</label>
						<div class="col-sm-10">
							<input type="text" name="ability" ng-model="moduleinfo.ability" class="form-control">
							<span class="help-block">模块功能描述, 使用简单的语言描述模块的作用, 来吸引用户</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"> 模块介绍</label>
						<div class="col-sm-10">
							<textarea type="text" name="description" ng-model="moduleinfo.description" class="form-control" rows="5">{{ moduleinfo.description }}</textarea>
							<span class="help-block">模块详细描述, 详细介绍模块的功能和使用方法</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"> 模块缩略图</label>
						<div class="col-sm-10">
							<div class="we7-input-img" ng-class="{ 'active' : moduleinfo.logo }" style="width: 100px;height: 100px;">
								<img ng-src="{{ moduleinfo.logo }}" ng-if="moduleinfo.logo">
								<a href="javascript:;" class="input-addon" ng-hide="moduleinfo.logo" ng-click="changePicture('logo')"><span>+</span></a>
								<input type="hidden" name="thumb">
								<div class="cover-dark">
									<a href="javascript:;" class="cut" ng-click="changePicture('logo')">更换</a>
									<a href="javascript:;" class="del" ng-click="delPicture('logo')"><i class="fa fa-times text-danger"></i></a>
								</div>
							</div>
							<span class="help-block">用 48*48 的图片来让你的模块更吸引眼球吧。仅支持jpg格式</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"> 模块封面</label>
						<div class="col-sm-10">
							<div class="we7-input-img" ng-class="{ 'active' : moduleinfo.logo}"  style="width: 100px;height: 100px;">
								<img ng-src="{{ moduleinfo.preview }}">
								<a href="javascript:;" class="input-addon" ng-click="changePicture('preview')"><span>+</span></a>
								<input type="hidden" name="thumb">
								<div class="cover-dark">
									<a href="javascript:;" class="cut" ng-click="changePicture('preview')">更换</a>
									<a href="javascript:;" class="del" ng-click="delPicture('preview')"><i class="fa fa-times text-danger"></i></a>
								</div>
							</div>
							<span class="help-block">模块封面, 大小为 600*350, 更好的设计将会获得官方推荐位置。仅支持jpg格式</span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<button  class="btn btn-primary" type="text" name="submit" ng-click="save()" data-dismiss="modal">保存</button>
					<input type="hidden" name="token" value="c781f0df">
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	require(['fileUploader'], function() {
		angular.module('moduleApp').value('config', {
			'isFounder' : '<?php  if($_W['isfounder']) { ?>1<?php  } else { ?>2<?php  } ?>',
			'receive_ban' : "<?php  echo $receive_ban;?>",
			'url' : '<?php  echo url('system/module/change_receive_ban')?>',
			'modulename' : '<?php  echo $module_info['name'];?>',
			'editModuleUrl' : "<?php  echo url('system/module/get_module_info')?>",
			'saveModuleUrl' : "<?php  echo url('system/module/save_module_info')?>"
	});
	angular.bootstrap($('.js-system-module-detail'), ['moduleApp']);
	});
</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>