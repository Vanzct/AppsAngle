<?php
class ReadConfig{
	static public $configFilePath='config.xml';
	public function readAllApp(){
		$xml=new DOMDocument();
		$xml->load("./config.xml");
		#appsArray
		$appArray=array();
		$apps = $xml->getElementsByTagName("app"); 
		foreach($apps as $app){
			$name=$app->getElementsByTagName("name"); 
			$path=$app->getElementsByTagName("path");
			$name_str = $name->item(0)->nodeValue; 
			$db_able = $name->item(0)->attributes->item(0)->value;
			$path_str = $path->item(0)->nodeValue;  
			$appArray[] = array("name"=>$name_str,"path"=>$path_str,"db"=>$db_able);
		}
		return $appArray;
	}
	public function readAppPath($app_name){
		$xml=new DOMDocument();
		$xml->load("./config.xml");
		#appsArray
		$apps = $xml->getElementsByTagName("app"); 
		foreach($apps as $app){
			$name=$app->getElementsByTagName("name"); 
			$path=$app->getElementsByTagName("path");
			$name_str = $name->item(0)->nodeValue;  
			if($name_str==$app_name)  
				return $path->item(0)->nodeValue;
			
		}
		return null;
	}
	public function readAppDbable($app_name){
		$xml=new DOMDocument();
		$xml->load("./config.xml");
		#appsArray
		$apps = $xml->getElementsByTagName("app"); 
		foreach($apps as $app){
			$name=$app->getElementsByTagName("name"); 
			$name_str = $name->item(0)->nodeValue; 
			$db_able = $name->item(0)->attributes->item(0)->value; 
			if($name_str==$app_name)  
				return $db_able;
			
		}
		return null;
	}
}
function test_readAllApp(){
	$rc = new ReadConfig();
	$apps = $rc->readAllApp();
	foreach($apps as $app){
		echo "name:",$app["name"],",path:",$app["path"],"<br>";
	}
}
function test_readAppPath(){
	$rc = new ReadConfig();
	echo $rc->readAppPath("Mobogenie");
}
//test_readAllApp();
//test_readAppPath();
?>
