<!DOCTYPE HTML>
<!DOCTYPE html PUBLIC "" ""><HTML lang="ja"><HEAD><META content="IE=11.0000" 
http-equiv="X-UA-Compatible">
 
<META charset="utf-8"> 
<META name="apple-mobile-web-app-capable" content="yes"> <TITLE>news 
detail</TITLE> <LINK href="/resources/bstyle.css" rel="stylesheet"> 
<LINK href="/resources/news.css" rel="stylesheet"> 
<STYLE>
    html, body {
    background-color: transparent;
  }
p{     
    background-image: url(/resources/bug_trans.png); 
}
</STYLE>
 
<SCRIPT type="text/javascript">
 window.onload = function() {
 setTimeout(function(){window.scrollTo(0, 1);}, 100);
 }

var strUA = "";
strUA = navigator.userAgent.toLowerCase();

if(strUA.indexOf("iphone") >= 0) {
  document.write('<meta name="viewport" content="width=880px, minimum-scale=0.45, maximum-scale=0.45" />');
} else if (strUA.indexOf("ipad") >= 0) {
  document.write('<meta name="viewport" content="width=1024px, minimum-scale=0.9, maximum-scale=0.9" />');
} else {
  document.write('<meta name="viewport" content="width=880px, minimum-scale=0.38, maximum-scale=0.38" />');
}
</SCRIPT>
 
<META name="GENERATOR" content="MSHTML 11.00.10011.0"></HEAD> 
<BODY>
<DIV id="wrapper">
<DIV class="title_news fs34"><SPAN class="ml30">抽卡详情</SPAN></DIV>
<DIV class="content_news">
<DIV class="note">
<P>这是点击“劝诱详细”按钮时默认显示的页面。<BR>编辑 webview/secretBox/index.php修改本页面的内容，或者在设置中按照说明指定新的页面。<BR></P>   </DIV></DIV>
<DIV class="footer_news fs34"><IMG width="100%" src="/resources/bg03.png"> 
</DIV></DIV>
 </BODY></HTML>