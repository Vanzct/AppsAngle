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
	#页面分为三个区域，头部，选择，和二维码显示区
	require_once "class.DB.php";
	require_once "config.php";
	require_once "class.ReadConfig.php";
	#质量平台页面
	define("LADAOBA","http://oa.it.mobogarden.com/login.php");
	if(isset($_GET["app_name"])){
		$app_name=$_GET["app_name"];
		setcookie("default_app",$app_name,time()+3600*24*30);
	}else {
		header("Location: index.php");
	}	
	#标题行
	echo "<div class='row first_row'>" ;
	echo "<div class='title_div'>";
	echo "<h3 class='page_title'>{$app_name}" ;

	$rc=new ReadConfig();	
	if((int)$rc->readAppDbable($app_name)==1)
		echo "<a class='c_model' href='home.php?app_name=",$app_name,"'>数据库模式<i class='entypo-forward'></i></a>";
	echo "</h3></div>";
	echo "<div class='to_orther'>";
	echo "<a class='to_ladaoba' href='",LADAOBA,"'>去集成质量管理平台<i class='entypo-cloud'></i></a>";
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
	<div class='row workspace'>
<?php					
	#选择区	
		$root_path=$rc->readAppPath($app_name);
		$selected_path="";
		$selected_dirs=array();
		if(isset($_GET["selected"])){
			$selected_dirs=explode("/" ,$_GET["selected"]);
			$selected_path = $_GET["selected"];
		}
		
		#已选条件
		echo "<div class='selected_div'>";
		echo "<ul class='selected_ul'><li><a class='filter_selected'href='home001.php?app_name=$app_name' >Home<i class='entypo-home'></i></a></li>";
		$back_path='';
		foreach($selected_dirs as $s){
			$back_path.=$s."/";
			echo "<li><a class='filter_selected' href='home001.php?app_name=",$app_name,"&selected=",substr($back_path,0,-1),"'>";
			echo "<i class='entypo-dot'></i><span>{$s}</span><i class='entypo-address'></i>   ";
			echo "</a></li>";
		}

		echo "</div>";
		
		if(strpos($selected_path,".apk")||strpos($selected_path,".ipa")){
			echo "<div class='qr001'>";
			echo "<div class='img_home001'>";
			$para=str_replace("/","_",$selected_path);
			$name=basename($selected_path);
			$path=str_replace($name,"",$selected_path);
			#下载路径
			$download_url="download.php?filepath=".$root_path.$selected_path;
			#触发统计路径
			$trigger_url= "angle.php?filepath=".$root_path.$selected_path;
			echo "<div class='qr_header'>";
			echo "<h4>文件名:<span>{$name}<span></h4>";
			echo "<p>路径：<span>{$path}</span></p>";
			echo "</div>";
			echo "<div class='img_div'>";
			#######这里生成二维码=======二维码的内容是  ：域名/download.php?app_path=应用路径 
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
			return;
		}
		echo "<div class='filter_div001'>";
		echo "<ul class='filter_ul'>";
		if(!($handle=opendir($root_path."/".$selected_path))) die("Can't open the directory.");
		while($file=readdir($handle)){
			if(isset($_GET["selected"])){
				$href="home001.php?app_name=".$app_name."&selected=".$_GET["selected"].'/'.$file;
			}else
				$href="home001.php?app_name=".$app_name."&selected=".$file;
			if(strpos($file,".apk")){ 
					echo "<li><div class='card'>","<div class='img_div'><a href='$href'><img src='images/android_logo03.png'></a></div>","<p><a href='$href'>$file","</a></p>","</div></li>";
			}elseif(strpos($file,".ipa")){ 
					echo "<li><div class='card'>","<div class='img_div'><a href='$href'><img src='images/ios_logo.png'></a></div>","<p><a href='$href'>$file","</a></p>","</div></li>"; 
			}elseif(strpos($file,".txt")){ 
				continue;
			}	
			elseif($file!="."&&$file!=".."){			
				echo "<li><div class='card'>","<div class='img_div'><a href='$href'><img src='images/folder001.PNG'></a></div>","<p><a href='$href'>$file","</a></p>","</div></li>";
			}
		}
		closedir($handle);
		echo "</ul>";
		echo "</div>";
	
?>	
</div>
</body>