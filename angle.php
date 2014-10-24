<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="xiaohai" content="width=device-width, initial-scale=1.0">
	
	<title>扫吗</title>
	<link rel="stylesheet" href="js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css" id="style-resource-1">
	<link rel="stylesheet" href="css/font-icons/entypo/css/entypo.css" id="style-resource-2">
	<link rel="stylesheet" href="css/bootstrap-min.css" id="style-resource-4">
	<link rel="stylesheet" href="css/neon-core-min.css" id="style-resource-5">
	<link rel="stylesheet" href="css/mobile.css" id="style-resource-9">
	
	<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>

</head>
<body class="page-body loaded">
<div id='main-content'>
<?php
	echo "<div class='row'>";
	$filepath=$_GET["filepath"];
	$name=basename($filepath);
	$path=str_replace($name,"",$filepath);
	
	echo "<h4>{$name}</h4>";
	echo "<p class='tip'>微信扫一扫无法直接下载，点击右上角，选择使用浏览器打开<span></span></p>";
	
	if(strpos($name,".apk"))
		$href="download.php?filepath=".$filepath;
	else{ 
		#创建plist
		require_once "class.WritePlist.php";
		require_once "config.php";
		$href=URL_DE."download.php?filepath=".$filepath;
		$para=str_replace("/","_",$filepath);
		$path="images/qr/".$para.".plist";
		WritePlist::write($path,$href,"com.17173.app","1",$name);
		$href="itms-services://?action=download-manifest&url=".$path;
	}
	echo "<div ><a class='d_btn' href='$href'>确认下载</a></div>";
	echo "</div>"; 
?>
</div>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F4fa6f5c543e643c686eadefba2c2efdf' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>