// JavaScript Document
$(function(){
$(".banner").slide({ titCell:".spot ul", mainCell:".pic ul", effect:"fold",  autoPlay:true, autoPage:true, trigger:"click" });
$(".footer-tab").slide({ titCell:".tab-li li", mainCell:".tab-con ul", effect:"fold", trigger:"click" });
$('.menu ul>li').hover(function () {
    $(this).children("dl.children").stop().animate({ opacity: '1', height: 'show' }, 0);
}, function () {
    $(this).children("dl.children").stop(true, true).hide();
});

$(".side-box ul li.customer").mousemove(function () {
    $(".kefu-qq").show().parents('.side-box').mouseleave(function () {
        $('.kefu-qq').hide()
    })
});

$(".kefu-qq span.close a").click(function(){ $(".side-box").removeClass('on');});
$(".casepage-list li").hover(function(){ $(this).addClass('on');},function(){ $(this).removeClass('on');});

$(".backtop").click(function(){$('body,html').animate({scrollTop:0},500); return false; });
$(".gototop li a").hover(function(){ $(this).stop().animate({"marginTop":"-40px"}, 150);},function(){ $(this).stop().animate({"marginTop":"0"}, 150);});
$(window).scroll(function(){
if ($(window).scrollTop()>130){
$(".backtop").stop().animate({"height":"40px"}, 300); }
else { $(".backtop").stop().animate({"height":"0"}, 300); } });
});