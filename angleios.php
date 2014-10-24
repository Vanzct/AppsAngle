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
	<link rel="stylesheet" href="css/mobile.css" id="style-resource-9">
	
	<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>

</head>
<body class="page-body loaded">
<div id='main-content'>
<?php
	require_once "config.php";
	require_once "class.DB.php";
	echo "<div class='row'>";
	$id=$_GET["id"];
	$db=new DB();
	$row=$db->selectAppQrsById($id);
	$name=basename($row["app_path"]);
	$time=$row["create_time"];
	$app_name=$row["app_name"];
	echo "<h4>{$name}</h4>";
	//echo "<p>{$time}</p>";
	echo "<p class='tip'>微信扫一扫无法直接下载，点击右上角，选择使用浏览器打开<span></span></p>";
	$href="itms-services://?action=download-manifest&url=".URL_DE.QR_IMG_ROOT_PATH.$app_name."/qr".$id.".plist";
	echo "<div ><a class='d_btn' href='{$href}'>确认下载</a></div>";
	echo "</div>"; 
?>
</div>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F4fa6f5c543e643c686eadefba2c2efdf' type='text/javascript'%3E%3C/script%3E"));
</script>
</body>