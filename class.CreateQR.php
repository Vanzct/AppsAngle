<?php
require_once "phpqrcode/phpqrcode.php"; 
class CreateQR{
	public static function create($data, $filepath){
		// 纠错级别：L、M、Q、H 
   		$errorCorrectionLevel = 'L';  
   		// 点的大小：1到10 
   		$matrixPointSize = 8;  
   		QRcode::png($data, $filepath, $errorCorrectionLevel, $matrixPointSize, 2);
	}
	public static function modify($logo,$QR,$output){
		if ($logo !== FALSE) {   
    		$QR = imagecreatefromstring(file_get_contents($QR));   
    		$logo = imagecreatefromstring(file_get_contents($logo));   
    		$QR_width = imagesx($QR);//二维码图片宽度   
    		$QR_height = imagesy($QR);//二维码图片高度   
    		$logo_width = imagesx($logo);//logo图片宽度   
    		$logo_height = imagesy($logo);//logo图片高度   
   			$logo_qr_width = $QR_width / 5;   
    		$scale = $logo_width/$logo_qr_width;   
    		$logo_qr_height = $logo_height/$scale;   
    		$from_width = ($QR_width - $logo_qr_width) / 2;   
    		#重新组合图片并调整大小   
    		imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,   
    		$logo_qr_height, $logo_width, $logo_height);   
		} 
		imagepng($QR, $output);  
	}
}
function test(){
	$cr=new CreateQR();
	$data="岭深常得蛟龙在，桐高自有凤凰栖；Think　different,瓯子阿达姆";
	$filepath="images/qr.png";
	$cr->create($data,$filepath);
	$cr->modify("images/logo.jpg","images/qr.png","images/qr_modified.png");
}
//test();
?>
