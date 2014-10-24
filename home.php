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
	<link rel="stylesheet" href="css/home002.css" id="style-resource-9">	
	<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
</head>
<body class="page-body loaded">
<div id='main-content'>

<?php
	require_once "class.DB.php";
	require_once "config.php";
	
	#质量平台页面
	define("LADAOBA","http://oa.it.mobogarden.com/login.php");
	if(isset($_GET["app_name"])){
		$app_name=$_GET["app_name"];
		setcookie("default_app",$app_name,time()+3600*24*30);
	}else {
		header("Location: index.php");
	}
	$db=new DB();
	$count=$db->selectAppQrsCount($app_name);
	$app_root_db=$db->selectAppRootByName($app_name);
	
	#标题行
	echo "<div class='row first_row'>" ;
	echo "<div class='title_div'>";
	echo "<h3 class='page_title'>{$app_name} <a class='c_model' href='home001.php?app_name=",$app_name,"'>文件夹库模式<i class='entypo-forward'></i></a></h3>" ;
	echo "<p class='howmuch'><a id='scan' href='#'>当前{$count[0]}个,扫描时间 {$app_root_db["update_time"]}<i class='entypo-arrows-ccw'></i></a></p>";
	
	echo "</div>";
	echo "<div class='to_orther'>";
	echo "<a class='to_ladaoba' href='".LADAOBA."'>去集成质量管理平台<i class='entypo-cloud'></i></a>";
	echo "<a class='to_home' href='index.php?clear_cookie=1' class='back'>返回重新选择应用<i class='entypo-home'></i></a>";
	echo "</div>";
	echo "</div>";
	?>
	<div class="row">
	<div class="progress">
		<div class="progress-bar progress-bar-success" role="progressbar"  style="width: 0%">
		<span class="sr-only"></span>
		</div>
	</div>
	</div>
	<?php					
	#选择和显示二维码区
	echo "<div class='row workspace'>";
	echo "<div class='scanning_pause'><div class='tip_div'><h3>正在扫描应用目录，请稍等几秒！</h3></div></div>";
	echo "<div class='filter_div'>";
	echo "<uL>";
	
	
	$qrs_db=$db->selectAllAppQrs($app_name);
	$levels_db=$db->selectAppLevel($app_name);
	$scandenies_db=$db->selectAllScanDenies($app_name);
	$root_path=$app_root_db["root_path"];	
	
	$level_alias=array();
	
	$selected_dirs=array();
	if(isset($_GET["selected"])){	
		$selected_dirs=explode("/" , $_GET["selected"]);			
	}
	
	$selected_path="";

	#补全默认选择
	$selected_count=count($selected_dirs);
	$levels_count=count($levels_db);
	
	
	$dir_level=array();
	for($i=0;$i<$levels_count;$i++){
		$next_dirs=array();
		foreach($qrs_db as $qr_db){
			$path=$qr_db["app_path"];	
			$deny=0;	
			foreach($scandenies_db as $deny){
				if(!strstr($path,$deny["path"])===false){
					$deny=1;
				}
			}
			if($deny==1) continue;
			#判断是否是已选择路径,过滤掉未被选择的
			if ($selected_path!=''&&!strpos($path,$selected_path)) continue;
			$path=str_replace($root_path."/","",$path);
			$paths = explode("/",$path);
			if($i>=count( $paths)) break;
			$dir_name = $paths[$i]; 
			if(!in_array($dir_name,$next_dirs))
				array_push($next_dirs,$dir_name);
		}

		if(count($next_dirs)<1) break;
		rsort($next_dirs);
		array_push($dir_level,$next_dirs);
		if(!isset($selected_dirs[$i])){
			array_push($selected_dirs,$next_dirs[0]);
			$selected_path.="/".$next_dirs[0];
		}	
		else 
			$selected_path.="/".$selected_dirs[$i];
	}

	#循环打印目录结构
	for($j=0;$j<count($dir_level); $j++){
		
		$one_level=$dir_level[$j];
		$href="home.php?app_name={$app_name}&selected=";
		for($i=0;$i<$j;$i++){
			$href.=$selected_dirs[$i]."/";
		}
		#只有一个就隐藏
		if(count($one_level)==1)continue;
		echo "<li>";
		echo"<div class='filter-line'>";
		echo "<h5 class='level_title'>{$levels_db[$j]["alias"]}</h5>";
		if(count($one_level)>6)
			echo "<p><a href='#' class='more'>更多</a></p>";
		echo "<hr>";
		echo "<ul class='filter_ul'>";
		$more=0;
		foreach($one_level as $one_dir){
			$more++;
			$h=$href.$one_dir;
			if($one_dir==$selected_dirs[$j])
				echo "<li><a href='{$h}' class='filter_key selected'>{$one_dir}</a></li>";
			else if($more>6)
				echo "<li class='hideit'><a href='{$h}' class='filter_key'>{$one_dir}</a></li>";
			else
				echo "<li><a href='{$h}' class='filter_key'>{$one_dir}</a></li>";
		}
		echo "</ul></div></li>";
	}
	echo "</ul></div>";
	
	#已选条件
	echo "<div class='selected_div'>";
	echo "<ul class='selected_ul'><li><a class='filter_selected' href='home.php?app_name=$app_name' >Home<i class='entypo-home'></i></a></li>";
	$back_path="home.php?app_name=$app_name"."&selected=";
	foreach($selected_dirs as $s){
		$back_path.=$s."/";
		$back_path1=substr($back_path,0,-1);
		echo "<li><a href='$back_path1' class='filter_selected'><i class='entypo-dot'></i><span>{$s}</span><i class='entypo-address'></i></a></li>";
	}
	echo "</ul>";
	echo "</div>";
	echo "<div class='row qr'>";
	echo "<div class='img_home'>";
	$para=str_replace("/","_",$selected_path);
	$name=basename($selected_path);
	$path=str_replace($name,"",$selected_path);
	#下载路径
	$download_url="download.php?filepath=".$root_path.$selected_path;
	#触发统计路径
	$trigger_url= "angle.php?filepath=".$root_path.$selected_path;
	echo "<div class='qr_header'>";
	//echo "<h4>文件名:<span>{$name}<span></h4>";
	//echo "<p>路径：<span>{$path}</span></p>";
	echo "</div>";
	echo "<div class='img_div'>";
	#这里生成二维码=======二维码的内容是  ：域名/download.php?app_path=应用路径 
	$str=URL_DE.$trigger_url;
	require_once "class.CreateQR.php";
	$createQR=new CreateQR();
	$filepath="images/qr/".$para.".png";
	#需要对中文转码成英文字符
	$createQR->create($str,$filepath);
	echo "<img src='$filepath'>";
	echo "</div>";
	echo "<div class='qr_footer'><a class='download_btn' data_url='{$trigger_url}' href='$download_url'>*点击下载到电脑*</a></div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";//workspace

?>
</div>

<script type="text/javascript" src="js/home.js"></script>
</body>