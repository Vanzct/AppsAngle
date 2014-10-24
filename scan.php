<?php
require_once "config.php";
require_once "class.DB.php"; 
class FileRead{
	static $scanning=0;
	public $appname;
	public $rootpath;
	public $maxcreatetime;
	public $db;
	#应用信息
	public $app;
	#目录层级
	public $levels;
	#不扫描的目录
	public $dirs_deny;
	#扫描到的应用
	public $app_count=0;
	function __construct(){
		$this->db=new DB();
	}	
	/**
	 * 将时间戳转换为 2014-09-04 13:13:17 格式
	 */
	public function dateFormate($data){
		$date123=date("Y-m-d H:i:s",$data);
		return $date123;
	}
	/**
	 * 前提后台以一定的频率在扫描
	 * 将过期版本和日期的文件夹剔除扫描
	 * 为版本排序，只扫描前 5 个版本的应用
	 * 只扫描今天的
	 */
	 
	public function readApkDirs($dirs,$level){	
		$files=array();
		foreach($dirs as $dir){ 				
			#有些不扫描
			if(in_array($dir,$this->dirs_deny)){
				continue;
			}
			$files=array_merge($files,$this->readChildrenFiles($dir,$level));
		}
		
		if(count($files)>0){
			#不再检查目录有几层
			/*$has=false;
			foreach($this->levels as $level_one){
				if($level_one["level"]==$level){
					$has=true;
					break;
				}
			}
			if(!$has){
				#echo "添加层级level_alias到数据库";
				$this->db->insertAppLevel($this->app["app_name"],$level,"第{$level}层");
			}*/
			$this->readApkDirs($files,$level+1);
		}
	}
	/**
	 * 读目录的所有子目录，如果遇到apk，按创建时间顺序存储
	 * 增加了去重功能
	 */
	function readChildrenFiles($dir,$level){			
			if(!($handle=opendir($dir)))  die("不能读文件目录".$dir);
			$files=array();
			while($file=readdir($handle)){
				$path=$dir."/".$file;
				if($file!="."&&$file!=".."){
					if(strrpos($file,".apk")>0){
						//echo "<br>",$path;
						$timestamp=filemtime($path);
						
						if($timestamp<$this->maxcreatetime){
							continue;
						}
						$time=$this->dateFormate($timestamp);
						$paths=explode("/",$path);
						$create_time=$paths[count($paths)-2];
						
						$id=$this->db->selectAppQrMaxId()+1;					
						$this->db->insertAppQr($id,$this->app["app_name"],$path,'',$time);
						echo $path;
						$this->app_count++;
					}else if(strrpos($file,".ipa")>0){
						#如果是只扫描目录结构则跳过
						if((int)$this->app["scan_dir"]==1){
							continue;
						}
						$timestamp=filemtime($path);
						#创建时间小于扫描时间，跳过
						if($timestamp<$this->app["update_time"]){
							continue;
						}
						#创建时间格式化
						$time=$this->dateFormate($timestamp);
						#根据老大的意思，时间取最后一层目录
						$paths=explode("/",$path);
						$create_time=$paths[count($paths)-2];
						#入库
						$id=$this->db->selectAppQrMaxId()+1;
						$qr_filename=QR_IMG_ROOT_PATH.$this->appname."/qr".$id.".png";
						$plist_filename=QR_IMG_ROOT_PATH.$this->appname."/qr".$id.".plist";
						#二维码里的数据
						$data=URL_DE."angleios.php?id=".$id;
						//$this->db->insertAppQr($id,$this->app["app_name"],$path,$data,$time);
						
						#创建二维码
						//$this->createQR($data,$qr_filename);
						#plist的下载地址
						//$url="download.php?filepath=".$path;
						//WritePlist::write($plist_filename,$url,"com.17173.app","1",$this->appname);
						array_push($this->show_apks,array("id"=>$id,"app_path"=>$path,"data"=>$data,"create_time"=>"刚刚"));	
					}
					else if(is_dir($path)){
						#子目录;					
						array_push($files,$path);				
					}
					
				}
			}
			closedir($handle);
			#版本层级，排序取前五
			if($level<=count($this->levels)&&$this->levels[$level]["alias"]=="版本"){
				rsort($files)	;
				if(count($files)>3)
					$files = array_slice($files,0,3);	
				
			}
			else if($this->levels[$level]["alias"]=="时间"){
				rsort($files)	;
				#只扫描最新一天的
				if(count($files)>0){ 
					$path000=str_replace($this->rootpath,"",$files[0]);
					
					$path_array=explode("/",$path000);
				
					$time=substr($path_array[$level+1],0,8);
					//echo $time,"<br>";
					$rfiles=array();
					foreach($files as $f){
						if(strpos($f,$time)){
							$rfiles[] = $f;
						}
					}	
					$files = $rfiles;
				}	
			}
			//print_r($files);
			return array_unique($files);
	}
}
$appname=$_GET["app_name"];

$scanMan=new FileRead();
$scanMan->appname=$appname;
#查询得到要扫描的目录
$app=$scanMan->db->selectAppRootByName($appname);
$scanMan->rootpath=$app["root_path"];

#如果其他人在刷新页面，结束
if((int)$app["isscanning"]==1){
	echo "state:0;其他小伙伴正在执行扫描...";
	return;
}
#设置刷新状态为--刷新中--，让其他人等待
$scanMan->db->updateAppRootIsScanning($appname,1);
$scandenies_db= $scanMan->db->selectAllScanDenies($appname);
$levels_db= $scanMan->db->selectAppLevel($appname);
$max_create_time_db = $scanMan->db->selectAppQrsCreatetime($appname);
$app["update_time"]=strtotime($app["update_time"]);
#初始化
$scanMan->app=$app;
$scanMan->levels=$levels_db;
$scanMan->maxcreatetime = strtotime($max_create_time_db['max_create_time']);
$scanMan->dirs_deny=array();

foreach($scandenies_db as $one){
	print_r($one);
	$scanMan->dirs_deny[]=$one["path"];
}

#开始计时
$start_time=time();
$scanMan->readApkDirs(array($app["root_path"]),0);
#结束计时
$end_time=time();
$scan_time=	($end_time-$start_time);
#扫描 结束 ，状态重置为可刷新
$scanMan->db->updateAppRootIsScanning($appname,0);
#扫描时间,存储开始时间，以防丢失应用
$scanMan->db->updateAppRootUpdateTime($appname,$scanMan->dateFormate($start_time));

echo "state:1;本次扫描发现".$scanMan->app_count."个新应用,用时".$scan_time."秒，点击确定刷新页面...";
?>
