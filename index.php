<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="xiaohai" content="width=device-width, initial-scale=1.0">
	
	<title>扫Ma</title>
	<link rel="stylesheet" href="js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css" id="style-resource-1">
	<link rel="stylesheet" href="css/font-icons/entypo/css/entypo.css" id="style-resource-2">
	<link rel="stylesheet" href="css/bootstrap-min.css" id="style-resource-4">
	<link rel="stylesheet" href="css/neon-core-min.css" id="style-resource-5">
	<link rel="stylesheet" href="css/neon-theme-min.css" id="style-resource-6">
	<link rel="stylesheet" href="css/neon-forms-min.css" id="style-resource-7">
	<link rel="stylesheet" href="css/custom-min.css" id="style-resource-8">
	<link rel="stylesheet" href="css/index.css" id="style-resource-9">	
</head>
<body class="page-body loaded">
<div id='main-content'>
<div class="content">
<?php
//显示应用选择标签
function showTile($app_name,$href,$count){
	echo "<div class='tile tile-title tile-blue' >" ;
	echo "<div class='icon'>";	
	echo "<a href='$href'><i class='entypo-mobile'></i></a>";
	echo "</div>";
	echo "<div class='title'>";
	echo "<h1><a href='$href'>{$app_name}</a></h1>";	
	echo "<p>有".$count."个应用</p>"; 
	echo "</div></div>";
}

//如果没有映射IP，显示所有APP Tile
if(isset($_GET["clear_cookie"])){
	setcookie("default_app",null, time()-3600);
}

if(!isset($_COOKIE["default_app"])||isset($_GET["clear_cookie"])){
	echo "<div class='tiles'>";
	echo "<div class='row title'>";
	echo "<h3>不管你选还是不选，我就在这里，不悲不泣...</h3>";
	echo "</div>";
	echo "<div class='row tiles_home'>";
	#不连接数据库，从配置文件读取内容

	require_once "class.ReadConfig.php";
	require_once "class.DB.php";
	$rc=new ReadConfig();	
	$apps=$rc->readAllApp();
	foreach($apps as $app){
		echo "<div class='col-xs-3'>";
		if((int)$app["db"]==1){ 
			$db=new DB();
			$count=$db->selectAppQrsCount($app["name"]);
			$db=null;
			$href="home.php?app_name={$app["name"]}" ;
			showTile($app["name"],$href,$count[0]);
		}else { 
			$href="home001.php?app_name={$app["name"]}" ;
			showTile($app["name"],$href,0);
		}echo "</div>";
	}	
}else if(isset($_COOKIE["default_app"])){
	$app_name=$_COOKIE["default_app"];
	require_once "class.ReadConfig.php";
	$rc=new ReadConfig();	
	if((int)$rc->readAppDbable($app_name)==1)
		header("Location: home.php?app_name=".$app_name);
	else 
		header("Location: home001.php?app_name=".$app_name);
}

?>
</div>
</div>
</body>