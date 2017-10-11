<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template("common/header", TEMPLATE_INCLUDEPATH)) : (include template("common/header", TEMPLATE_INCLUDEPATH));?>
<div class="clearfix">
	<h5 class="page-header">安装 <?php  if($action == 'module') { ?><?php  echo $module['title'];?><?php  } else { ?>模板<?php  } ?></h5>
	<div class="alert alert-info">
		您正在安装 <?php  if($action == 'module') { ?><?php  echo $module['title'];?> 模块<?php  } else { ?>模板<?php  } ?>. 请选择哪些公众号服务套餐组可使用
		<?php  if($action == 'module') { ?><?php  echo $module['title'];?> 功能<?php  } else { ?>该模板<?php  } ?> .
	</div>
	<div class="alert alert-info">
		默认将<?php  if($action == 'module') { ?><?php  echo $module['title'];?> 模块<?php  } else { ?>模板<?php  } ?>加入<span class="label label-info">所有服务</span>套餐服务
	</div>
	<form class="form-horizontal form we7-form" action="" method="post" id="form1">
		<h5 class="page-header">可用的公众号服务套餐组 <small>这里来定义哪些公众号服务套餐组可使用 <?php  if($action == 'module') { ?><?php  echo $module['title'];?> 功能<?php  } else { ?>该模板<?php  } ?></small></h5>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">公众号服务套餐组</label>
			<div class="col-sm-10 col-xs-12">
				<?php  if(is_array($module_group)) { foreach($module_group as $group) { ?>
				<div class="checkbox">
					<input id="checkbox-<?php  echo $group['id'];?>" type="checkbox" name="group[]" value="<?php  echo $group['id'];?>">
					<label for="checkbox-<?php  echo $group['id'];?>" class="ng-binding"><?php  echo $group['name'];?></label>
				</div>
				<?php  } } ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"></label>
			<div class="col-sm-10 col-xs-12">
				<input type="submit" class="btn btn-primary" name="submit" value="确定继续安装 <?php  echo $module['title'];?>">
			</div>
		</div>
		<input type="hidden" name="flag" value="1">
		<input type="hidden" name="tid" value="<?php  echo $tid;?>">
	</form>
	<script>
		$('#form1').submit(function(){
			var num = $("input[name='group[]']:checked").length;
			if(num == 0) {
				return confirm("您没有选择可使用该模块/模板的公众号服务套餐组,模块/模板安装成功后可在 公众号服务套餐 编辑");
			}
			return true;
		});
	</script>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
