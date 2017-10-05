;
// 百度统计事件
var _hmt = _hmt || [];
_hmt.push(["_setUserId", "jioufenxiao1"]);

// CNZZ统计事件
var _czc = _czc || [];

// 客服QQ列表
var QQList =[
	['Luxj', { name: 'Luxj', qq: '3185055060' }],	
];

// 全局变量，记录该访客是否触发过事件
SessionEventTriggered_COOKIE_NAME = "EventTriggered";

// 根据QQ名称获取客服QQ对象
function getQQ(name)
{	
	for	(i=0; i<QQList.length; i++)
	{
		if	(QQList[i][0]===name)
		{
			return QQList[i][1];
		}			
	}
}
	
// 获取该访客是否触发过事件，返回：是——true，否——false
function isSessionEventTriggered(){
	return  $.cookie(SessionEventTriggered_COOKIE_NAME) == "true";
}
// 设置该访客是否触发过事件，传入：是——true，否——false
function setSessionEventTriggered(val){
	return  $.cookie(SessionEventTriggered_COOKIE_NAME, val, { expires: 1, path:'/' }) ;
}

// create by zhaosh in 20160516
// 追踪搜索引擎
var trackSearchEngine = getTrackSearchEngine();

// 判断用户的打开行为（直接打开和各种搜索引擎）
function getTrackSearchEngine ()
{
	//全局变量
	SearchEngine_COOKIE_NAME = "SearchEngine";
	ReferDomian_COOKIE_NAME = "ReferDomian";
	ReferUrl_COOKIE_NAME = "ReferUrl";
	
	if($.cookie(SearchEngine_COOKIE_NAME))
	{
		return $.cookie(SearchEngine_COOKIE_NAME);
	}
	else{
	
		if(!document.referrer){
			$.cookie(SearchEngine_COOKIE_NAME, 'DIRECT', { expires: 1, path:'/' });
			return $.cookie(SearchEngine_COOKIE_NAME);
		}else{
			$.cookie(SearchEngine_COOKIE_NAME, getSearchEngine()== "SEM" ? "SEM_" + getReferUrl() : getSearchEngine(), { expires: 1, path:'/' });
			return $.cookie(SearchEngine_COOKIE_NAME);
		}
	}
}

// 从refer获取来源链接
function getReferUrl() {
	
	if($.cookie(ReferUrl_COOKIE_NAME))
	{
		return $.cookie(ReferUrl_COOKIE_NAME);
	}
	else{
		if(!document.referrer){
			$.cookie(ReferUrl_COOKIE_NAME, 'DIRECT', { expires: 1, path:'/' });
			return $.cookie(ReferUrl_COOKIE_NAME);
		}else{
			$.cookie(ReferUrl_COOKIE_NAME, document.referrer, { expires: 1, path:'/' });
			return $.cookie(ReferUrl_COOKIE_NAME);
		}
	}
}

// 从refer获取来源域名
function getReferDomian() {
	
	if($.cookie(ReferDomian_COOKIE_NAME))
	{
		return $.cookie(ReferDomian_COOKIE_NAME);
	}
	else{
	
		if(!document.referrer){
			$.cookie(ReferDomian_COOKIE_NAME, 'DIRECT', { expires: 1, path:'/' });
			return $.cookie(ReferDomian_COOKIE_NAME);
		}else{
			var strLength = document.referrer.length;
			var strUrl = document.referrer.substring(7, strLength);
			var referDomian =strUrl.substring(0, strUrl.indexOf("/"));
			$.cookie(ReferDomian_COOKIE_NAME, referDomian, { expires: 1, path:'/' });
			return $.cookie(ReferDomian_COOKIE_NAME);
		}
	}
}

// 从refer获取搜索引擎信息
function getSearchEngine ()
{
	// 如果是来路域名为其它域名，默认为从SEM过来的
	var result = "SEM" ;
	var macth = new  Array();
	macth.push(["baidu","BAIDU"]);
	macth.push(["sogou","SOGOU"]);
	macth.push(["so.com","360"]);
	macth.push(["360.cn","360"]);
	macth.push(["google","GOOGLE"]);
	macth.push(["bing.com","BING"]);
	
	var ref=document.referrer;
	for(var i=0; i< macth.length; i++)
	{
		if(ref.indexOf(macth[i][0])>-1)
		{
			if(macth[i][1]=="BAIDU"){
				if(ref.indexOf('/baidu.php?') > -1){
					//百度竞价
					result = "BAIDU_PPC";
				}else if(ref.indexOf('/link?') > -1){
					//百度自然流量
					result = "BAIDU_SEO";
				}else{			
					result = "BAIDU_OTHER";
				}
			}else if(macth[i][1]=="360"){
				if(ref.indexOf('/search/eclk?') > -1){
					//360竞价
					result = "360_PPC";					
				}else if(ref.indexOf('/link?') > -1){	
					//360自然流量
					result = "360_SEO";
				}else{
					result = "360_OTHER";
				}
			}else{
				result =  macth[i][1];
			}
			break;
		}
	}
	return result;
}

// trackEvent事件处理
// 以下值从页面传参：
// trackInnerCategory，事件内部分类，取值为：
	// PageQQ：页面非具名的QQ，程序会内部轮换
	// TargetQQ：特指某个具名的QQ
	// RegisterMsg：申请注册
	// JoinMsg：申请加盟	
// trackLabel：对应TrackEvent事件的Label参数，取值为：
	// 当trackInnerCategory为TargetQQ时，该值传入客服QQ名称
	// 当trackInnerCategory为其他值时，该值不从页面传参	
// trackPage：事件页面，传入页面主文件名，假如是QQ面板事件，则传入“Panel”
// trackNode：事件页面的节点，传入页面节点名称，假如是QQ面板事件，则无需传入

// 以下值不从页面传参，由程序自动生成：
// trackCategoryBaidu：对应TrackEvent事件的Category参数（百度统计）
// trackCategoryCNZZ：对应TrackEvent事件的Category参数（CNZZ统计）
// trackActionBaidu：对应TrackEvent事件的Action参数（百度统计）
// trackActionCNZZ：对应TrackEvent事件的Action参数（CNZZ统计）
$(function (){	
	$('.trackEvent').click(function (){	
		var trackInnerCategory = $(this).attr('trackInnerCategory');
		var trackPage = $(this).attr('trackPage');
		var trackNode = $(this).attr('trackNode');	
		
		var trackCategoryBaidu;
		var trackCategoryCNZZ;
		var trackActionBaidu;
		var trackActionCNZZ;
		var trackLabel;
		
		switch(trackInnerCategory)
		{		
			case 'PageQQ':
			case 'TargetQQ':
			trackCategoryBaidu = 'QQ';	
			trackCategoryCNZZ = 'QQ';
			
			var currentQQ;
			
			if( trackInnerCategory === 'PageQQ' )
			{
				// switch(Math.floor(new Date().getMinutes()/20))
				// {					
					// case 0:
					// currentQQ = getQQ('Hanzw');
					// break;
					
					// case 1:			
					// currentQQ = getQQ('Zhudc');
					// break;					
					
					// case 2:
					// currentQQ = getQQ('Douwl');
					// break;
					
					// case 2:			
					// currentQQ = getQQ('Zhudc2');
					// break;			
				// }
				
				currentQQ = getQQ('Luxj');
				trackLabel = currentQQ.name;
			}
			else if( trackInnerCategory === 'TargetQQ' )
			{				
				// 特定的QQ，页面上用trackLabel参数传QQ名称过来
				trackLabel = $(this).attr('trackLabel');
				currentQQ = getQQ(trackLabel);	
			}	
			
			// 由于百度统计的特殊性（长期没发生的事件就会从服务器端消失，导致转化捕捉有漏），这里需要简化trackActionBaidu的取值
			if( $.trim(trackPage) === 'Panel' )
			{
				trackActionBaidu = 'Panel';
			}
			else
			{
				trackActionBaidu = 'Page';
			}
			
			if( $.trim(trackNode) != '' )
			{
				trackActionCNZZ = trackPage + '_' + trackNode;					
			}
			else
			{
				trackActionCNZZ = trackPage;			
			}
			
			// 弹出QQ对话框
			// $(this).attr('target', '_blank');	
			$(this).attr('href', 'tencent://message/?uin=' + currentQQ.qq + '&Site=qq&Menu=yes');
			break;
			
			case 'RegisterMsg':
			trackCategoryBaidu = "Msg";
			trackCategoryCNZZ = "Msg";
			trackActionBaidu = "Register";
			trackActionCNZZ = "Register";
			trackLabel = trackPage;
			break;	
			
			case 'JoinMsg':
			trackCategoryBaidu = "Msg";
			trackCategoryCNZZ = "Msg";
			trackActionBaidu = "Join";
			trackActionCNZZ = "Join";
			trackLabel = trackPage;
			break;			
		}
		
		// 设置事件来路
		trackCategoryCNZZ = trackCategoryCNZZ + '_' +  trackSearchEngine;	
		
		// 判断该访客是否触发过事件，未触发过的访客即是新访客
		if( isSessionEventTriggered() != true )
		{				
			// 触发百度的trackEvent事件,百度统计只记录来自竞价排名的事件
			if(trackSearchEngine.indexOf('BAIDU_PPC') > -1)
			{
				_hmt.push(['_trackEvent', trackCategoryBaidu, trackActionBaidu, trackLabel]);
			}
			
			// 触发CNZZ的trackEvent事件，新访客记1分
			_czc.push(['_trackEvent', trackCategoryCNZZ, trackActionCNZZ, trackLabel, 1]);	
		}
		else
		{
			// 触发CNZZ的trackEvent事件，老访客记0分
			_czc.push(['_trackEvent', trackCategoryCNZZ, trackActionCNZZ, trackLabel, 0]);			
		}
		
		// 标记该访客已触发过事件
		setSessionEventTriggered(true);
	});
})