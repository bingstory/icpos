<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php  if(isset($title)) $_W['page']['title'] = $title?><?php  if(!empty($_W['page']['title'])) { ?><?php  echo $_W['page']['title'];?><?php  } ?><?php  if(empty($_W['page']['copyright']['sitename'])) { ?><?php  if(IMS_FAMILY != 'x') { ?><?php  if(!empty($_W['page']['title'])) { ?> - <?php  } ?>微信活动运维系统 -  Powered by WE8<?php  } ?><?php  } else { ?><?php  if(!empty($_W['page']['title'])) { ?> - <?php  } ?><?php  echo $_W['page']['copyright']['sitename'];?><?php  } ?></title>
	<meta name="keywords" content="<?php  if(empty($_W['page']['copyright']['keywords'])) { ?><?php  if(IMS_FAMILY != 'x') { ?>微信活动运维系统<?php  } ?><?php  } else { ?><?php  echo $_W['page']['copyright']['keywords'];?><?php  } ?>" />
	<meta name="description" content="<?php  if(empty($_W['page']['copyright']['description'])) { ?><?php  if(IMS_FAMILY != 'x') { ?>微信活动运维系统微信活动运维系统，简称微信，微信是一款基于微信营销免费开源的活动运维系统，为现代智慧活动场景提供最为高效、完善、靠谱的saas互联网技术解决方案。<?php  } ?><?php  } else { ?><?php  echo $_W['page']['copyright']['description'];?><?php  } ?>" />
	<link rel="shortcut icon" href="<?php  if(!empty($_W['setting']['copyright']['icon'])) { ?><?php  echo $_W['attachurl'];?><?php  echo $_W['setting']['copyright']['icon'];?><?php  } else { ?>./resource/images/favicon.ico<?php  } ?>" />
	<link href="./resource/css/bootstrap.min.css?v=20170619" rel="stylesheet">
	<link href="./resource/css/common.css?v=20170619" rel="stylesheet">
	<script type="text/javascript">
	if(navigator.appName == 'Microsoft Internet Explorer'){
		if(navigator.userAgent.indexOf("MSIE 5.0")>0 || navigator.userAgent.indexOf("MSIE 6.0")>0 || navigator.userAgent.indexOf("MSIE 7.0")>0) {
			alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
		}
	}
	window.sysinfo = {
		<?php  if(!empty($_W['uniacid'])) { ?>'uniacid': '<?php  echo $_W['uniacid'];?>',<?php  } ?>
		<?php  if(!empty($_W['acid'])) { ?>'acid': '<?php  echo $_W['acid'];?>',<?php  } ?>
		<?php  if(!empty($_W['openid'])) { ?>'openid': '<?php  echo $_W['openid'];?>',<?php  } ?>
		<?php  if(!empty($_W['uid'])) { ?>'uid': '<?php  echo $_W['uid'];?>',<?php  } ?>
		'isfounder': <?php  if(!empty($_W['isfounder'])) { ?>1<?php  } else { ?>0<?php  } ?>,
		'siteroot': '<?php  echo $_W['siteroot'];?>',
		'siteurl': '<?php  echo $_W['siteurl'];?>',
		'attachurl': '<?php  echo $_W['attachurl'];?>',
		'attachurl_local': '<?php  echo $_W['attachurl_local'];?>',
		'attachurl_remote': '<?php  echo $_W['attachurl_remote'];?>',
		'module' : {'url' : '<?php  if(defined('MODULE_URL')) { ?><?php echo MODULE_URL;?><?php  } ?>', 'name' : '<?php  if(defined('IN_MODULE')) { ?><?php echo IN_MODULE;?><?php  } ?>'},
		'cookie' : {'pre': '<?php  echo $_W['config']['cookie']['pre'];?>'},
		'account' : <?php  echo json_encode($_W['account'])?>,
	};
	</script>
	<script>var require = { urlArgs: 'v=20170619' };</script>
	<script type="text/javascript" src="./resource/js/lib/jquery-1.11.1.min.js?v=20170619"></script>
	<script type="text/javascript" src="./resource/js/lib/bootstrap.min.js?v=20170619"></script>
	<script type="text/javascript" src="./resource/js/app/util.js?v=20170619"></script>
	<script type="text/javascript" src="./resource/js/app/common.min.js?v=20170619"></script>
	<script type="text/javascript" src="./resource/js/require.js?v=20170619"></script>
</head>
<body>
	<div class="loader" style="display:none">
		<div class="la-ball-clip-rotate">
			<div></div>
		</div>
	</div>
