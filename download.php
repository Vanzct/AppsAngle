<?php
if(!isset($_GET["filepath"])){
	 echo "--没有文件路径--";
	 die("文件路径有误"); 
}
$filepath =$_GET["filepath"];
//文件名限制
$prefix=array(".apk",".ipa",".plist",".exe");
$is_name_ok=false;
foreach($prefix as $p){
	if(strpos($filepath,$p)>0) {
		$is_name_ok=true;
		break;
	}
}
if(!$is_name_ok&&!file_exists($filepath)){  
	die("文件路径有误"); 
	exit;
}
else {
	$file = fopen($filepath,"r"); // 打开文件
	// 输入文件标签
	Header("Content-type: application/octet-stream");
	Header("Accept-Ranges: bytes");
	Header("Accept-Length: ".filesize($filepath));
	Header("Content-Disposition: attachment; filename=" . basename($filepath));
	echo fread($file,filesize($filepath));
	fclose($file);
	exit;
}
?>
