<?php
define("CONTROLLER", "webview");
date_default_timezone_set("Asia/Tokyo");
header("X-Powered-By: Project Custom Festival");
header("Y-Powered-By: LLS/0.3");
header('Server: LLS/0.3');
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Copyright: PCF@2018');

require(__DIR__.'/../includes/includeCommon.php');
require(__DIR__.'/../includes/passwordUtil.php');
//HTTPS强制跳转
if($config->reg['enable_ssl'] && $_SERVER['HTTPS'] != 'on') {
	header('Location: https://'.$config->reg['ssl_domain'].$_SERVER['REQUEST_URI']);
	exit();
}

$mysql->query('START TRANSACTION');
session_start();
//第一次访问把所有内容全存进session
if (isset($_SERVER['HTTP_AUTHORIZE'])) {
	$_SESSION['server'] = $_SERVER;
	$authorize_ = $_SERVER['HTTP_AUTHORIZE'];
	$uid = $_SERVER['HTTP_USER_ID'];
}else if(isset($_SESSION['server'])){
	$authorize_ = $_SESSION['server']['HTTP_AUTHORIZE'];
	$uid = $_SESSION['server']['HTTP_USER_ID'];
}else{
	header('HTTP/1.1 403 Forbidden');
	print("<h1>出现了一些问题，请尝试关闭页面重新打开</h1>");
	exit();
}


//处理authorize

$authorize = [];

$authorize_ = explode("&", $authorize_);
foreach($authorize_ as $i){
	$j = explode("=", $i);
	$authorize[$j[0]] = $j[1];
}

//default page
if(!isset($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO']=='') {
	$module = 'announce';
	$action = 'index';
} else {
	$path = explode('/', $_SERVER['PATH_INFO']);
	$module = $path[1];
	$action = $path[2];
}

//file exist
$pagePath = sprintf(__DIR__."/../webview/page/%s/%s.php", $module, $action);
if(!file_exists($pagePath)) {
	header('HTTP/1.1 404 Not Found');
	print("<h1>404 Not Found</h1>");
	exit();
}

//module break
if($module != 'maintenance'){
	require_once("../webview/module/".$module.".php");
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="mobile-web-app-capable" content="yes">
	<meta content="black-translucent" name="apple-mobile-web-app-status-bar-style">
	<meta http-equiv="Cache-Control" content="no-siteapp"/>
	<meta name="apple-mobile-web-app-title" content="LLSupport">
	<meta content="telephone=no" name="format-detection"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>PCF_WEBVIEW_<?=$module.'/'.$action?></title>
    <!-- CSS -->
	<link href="/assets/css/mdui.min.css?v=<?=time()?>" rel="stylesheet" />
	<link href="/assets/css/doc.css?v=<?=time()?>" rel="stylesheet" />
	<link href="/assets/css/main.css?v=<?=time()?>" rel="stylesheet"/>
</head>
<body class="mdui-loaded mdui-locked mdui-theme-primary-pink mdui-theme-accent-pink" style="overflow-y: auto !important;">
	<?php require_once '../webview/page/'.$module.'/'.$action.'.php'; ?>
	<!-- Script -->
	<script src="/assets/js/smooth-scroll.js?v=<?=time()?>"></script>
	<script src="/assets/js/holder.js?v=<?=time()?>"></script>
	<script src="/assets/js/highlight.js?v=<?=time()?>"></script>
	<script type="text/javascript" src="/assets/js/mdui.js?v=<?=time()?>" ></script>
	<script>var $$ = mdui.JQ;</script>
	<script src="/assets/js/main.js?v=<?=time()?>"></script>
	<script type="text/javascript">
	</script>
</body>
</html>

