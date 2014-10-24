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
	<link rel="stylesheet" href="css/neon-theme-min.css" id="style-resource-6">
	<link rel="stylesheet" href="css/neon-forms-min.css" id="style-resource-7">
	<link rel="stylesheet" href="css/custom-min.css" id="style-resource-8">
	<link rel="stylesheet" href="css/manager.css" id="style-resource-9">	
	<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
</head>
<body class="page-body loaded">
<div id='main-content'>
<div class="content">
<?php
require_once "class.DB.php";
	if(isset($_GET["app_name"])){
		$app_name=$_GET["app_name"];
		
	}else {
		header("Location: index.php");
	}
	$db=new DB();
	$count=$db->selectAppQrsCount($app_name);
	#标题行
	echo "<div class='row first_row'>" ;
	echo "<h3 class='page_title'>{$app_name}</h3>" ;
	echo "<p class='howmuch'>当前有{$count[0]}个应用</p>";
	echo "<p><a href='scan.php'>扫描应用</a></p>";
	echo "</div>";
	
	
	
	$app_root_db=$db->selectAppRootByName($app_name);
	$qrs_db=$db->selectAppQrsSEO($app_name);
	$dirs_db=$db->selectAppDirs($app_name);
	$levels_db=$db->selectAppLevel($app_name);
	
	$root_path=$app_root_db["root_path"];	
	
	$selected_dirs=array();
	if(isset($_GET["selected"])){
		$selected_dirs=explode(",",$_GET["selected"]);
	}
	#已选条件
	echo "<div class='selected_div'>";
	echo "<ul class='selected_ul'><li>已选路径:</li><li><a href='manager?app_name={$app_name}'>Home<i class='entypo-home'></i></a></li>";
	for($i=0;$i<count($selected_dirs) ;$i++){
		$selected=$selected_dirs[0];
		for($j=1;$j<=$i;$j++){
			$selected.=$selected_dirs[$j];
		}
		echo "<li><a href='manager?app_name={$app_name}&selected={$selected}'>{$selected_dirs[$i]}<i class='entypo-flow-tree'></i></a></li>";
	}
	echo "</ul>";
	echo "</div>";
	$preg="/";
	foreach($selected_dirs as $selected){
		$preg.="\/".$selected;
	}
	if($preg=="/"){
		$preg.=".*/";
	}else $preg.="/";

	#这里命名层级名称
	echo "<input type='text' value='{$levels_db[count($selected_dirs)]["alias"]}'>";
	echo "<hr>";
	echo "<uL>";
	# 把每一层的子目录筛选出来
	$dirs_show=array();
	foreach($qrs_db as $qr){
	if(!preg_match($preg,$qr["app_path"])) continue;
		$path=str_replace($root_path,"",$qr["app_path"]);
		$paths=explode("/",$path);
		#取下一级目录		
		$name=$paths[count($selected_dirs)+1];
			
		if(!in_array($name,$dirs_show)){
			array_push($dirs_show,$name);
		}
			
	}
	foreach($dirs_show as $dir){
		if(isset($_GET["selected"]))
			$selected=$_GET["selected"].",".$dir;
		else
			$selected=$dir;
		echo "<li><div class='card'>";
		echo "<p><a href='manager.php?app_name={$app_name}&selected={$selected}' class='' ><span>{$dir}</span></a></p>";
		echo "<p><a href='manager.php?app_name={$app_name}&selected={$selected}' class='' >扫描</a></p>";
		echo "<p><a href='manager.php?app_name={$app_name}&selected={$selected}' class='' >不显示</a></p>";
		echo "</div></li>";
	}
		
		echo "</ul>";
		echo "</li>";
	
	echo "</ul></div>";
?>
</div>
</div>
</body>