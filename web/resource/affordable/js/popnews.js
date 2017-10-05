;(function (global){
	global.popnews={
		timeName:null,
		messageTips: function (text, option) {//text=提示文字，option=可选参数
		    var className = 'default', showTime, messageBox, callback, propmtTxt = "";
		    callback = option.callback ? option.callback : null;
			if(option){
				if(option.type=="error"||option.type=="success"||option.type=="warning"){
					className=option.type;
				}
				showTime=option.showTime?option.showTime:2000;//是否传递消失时间参数，默认2000毫秒
				switch(option.type){
					case 'error':
						//propmtTxt="错误";
						break;
					case 'success':
						//propmtTxt="成功";
						break;
					case 'warning':
						//propmtTxt="警告";
						break;
				}
			}
			messageShow(text, className, propmtTxt, showTime, callback);
		},
		confirm:function(text,btnText,callback){
			var confirmHtml='<div class="popnews-mask">'+//生成html拼接
								'<div class="popnews-confirm">'+
									'<div class="popnews-confirm-h"></div>'+
									'<div class="popnews-confirm-b">'+
										'<div class="confirm-b-in">'+
											'<h3>'+text+'</h3>'+
										'</div>'+
									'</div>'+
									'<div class="popnews-confirm-f">'+
										'<button type="" class="confirm-ok">'+btnText[0].text+'</button>'+
										'<button type="" class="confirm-no">'+btnText[1].text+'</button>'+
									'</div>'+
								'</div>'+
							'</div>';
			$('body').append(confirmHtml);//插入到html
			$('.popnews-mask').fadeIn(200).find('.popnews-confirm').animate({//执行动画显示出来
				'opacity':1,
				'top':'100px'
			},400);
			$('.popnews-mask').on('click',function (){
				$('.popnews-mask').fadeOut(200).find('.popnews-confirm').animate({//执行动画显示出来
					'opacity':0,
					'top':'-100%'
				},400,function (){
					$('.popnews-mask').remove();
					$('body').off();
					$('.popnews-mask').off();
				});
			});
			$('.popnews-mask .popnews-confirm').on('click',function (e){
				e.stopPropagation();
			});
			$('.popnews-confirm button').click(function (e){
				e.stopPropagation();
				var btnT=$(this).text();//获取当前点击按钮的值
				e.index=0;//初始化点击的是哪个
				$.each(btnText,function (i){//根据传进来的值点击的是确定还是取消
					if(this.text==btnT&&this.confirm){
						e.index=1;
						return
					}
				});
				$('.popnews-mask').fadeOut(200).find('.popnews-confirm').animate({//执行动画显示出来
					'opacity':0,
					'top':'-100%'
				},400,function (){
					$('.popnews-mask').remove();
					$('body').off();
					$('.popnews-mask').off();
				});
				callback(e);//执行回调把事件（e为了回调函数点击得到是否）
			});
		}
	};
	function messageShow(text,className,propmtTxt,showTime,callback){
		clearTimeout(global.popnews.timeName);
		if($('.alert-message')[0]){
			messageBox.remove();
			createMessage(text,className,propmtTxt,showTime);
		}else{
			createMessage(text,className,propmtTxt,showTime);
		}
		global.popnews.timeName=setTimeout(function (){
			messageBox.fadeOut(500,function (){
			    messageBox.remove();
			    if (callback) callback();
			});
		},showTime);
	};
	function createMessage(text,className,propmtTxt,showTime){
		$('body').append('<div class="alert-message '+className+'"><p><span>'+propmtTxt+'</span>'+text+'</p></div>');
		messageBox=$('.alert-message');
		messageBox.fadeIn(500).css({
			'marginLeft':-messageBox.outerWidth()/2+'px'
		});
	};
})(window);