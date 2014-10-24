<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="xiaohai" content="width=device-width, initial-scale=1.0">
	
	<title>扫一扫</title>
	<link rel="stylesheet" href="js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css" id="style-resource-1">
	<link rel="stylesheet" href="css/font-icons/entypo/css/entypo.css" id="style-resource-2">
	<link rel="stylesheet" href="css/bootstrap-min.css" id="style-resource-4">
	<link rel="stylesheet" href="css/neon-core-min.css" id="style-resource-5">
	<link rel="stylesheet" href="css/index.css" id="style-resource-9">	
</head>
<body class="page-body loaded">
<div id='main-content'>
<div class="content">
<?php
require_once "config.php";
/**
 * 显示应用选择标签
 */
function showTile($app_name,$count){
	echo "<div class='tile tile-title tile-blue' >" ;
	echo "<div class='icon'>";	
	echo "<a href='home001.php?app_name={$app_name}'><i class='entypo-mobile'></i></a>";
	echo "</div>";
	echo "<div class='title'>";
	echo "<h1><a href='home001.php?app_name={$app_name}'>{$app_name}</a></h1>";
	if($count!=null)	
		echo "<p>有".$count."个应用</p>"; 
	else 
		echo "<p>***</p>"; 
	echo "</div></div>";
}
#Cookie没有记录默认应用
if(!isset($_COOKIE["default_app"])){
	echo "<div class='tiles'>";
	echo "<div class='row title'>";
	echo "<h3>不管你选还是不选，我就在这里，不悲不泣...</h3>";
	echo "</div>";
	echo "<div class='row tiles_home'>";
	#不连接数据库，从配置文件读取内容

	require_once "class.ReadConfig.php";
	$rc=new ReadConfig();	
	$apps=$rc->readAllApp();
	foreach($apps as $app){
		echo "<div class='col-xs-3'>";
		showTile($app["name"],null);
		echo "</div>";
	}	
}else if(isset($_COOKIE["default_app"])){
	$appname=$_COOKIE["default_app"];
	header("Location: home001.php?app_name=".$appname);
}
?>
</div></div>
</body>