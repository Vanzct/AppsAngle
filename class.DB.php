<?php
/*
 * 连接数据库基类，默认连接63
 */
require_once "config.php";
class DBConn{
	
 	private $_conn=null;

 	public function getConn($schema=DB_DSN_APPS,$username=DB_USERNAME,$password=DB_PASSWORD){
 		try{
 			$this->_conn=new PDO($schema,$username,$password);
 			$this->_conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 			$this->_conn->query("set names utf8");
 			return $this->_conn;
 		}catch(PDOException $e){
 			echo "DBConn getConn() fail:".$e->getMessage();
 			$_conn=null;
 		}	
 	}
 	function __desctruct(){
 		$this->_conn=null;
 	}
 	
}

class DB extends DBConn{
	/**
	 * 查询所有应用名和根目录
	 */
	public function selectAppRoot(){
		$conn=parent::getConn();
 		$sql="select app_name,root_path from app_root ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetchAll();
 			return $row;
 		}catch(PDOException $e){
 			echo "failure:".$e->getMessage();
 			return false;
 		}			
	}
	/**
	 * 查询所有应用名和根目录
	 */
	public function selectAppRootByName($app_name){
		$conn=parent::getConn();
 		$sql="select app_name,root_path,update_time,isscanning,scan_dir from app_root where app_name='{$app_name}' ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetch();
 			return $row;
 		}catch(PDOException $e){
 			echo "failure:".$e->getMessage();
 			return false;
 		}			
	}
	
	public function updateAppRootUpdateTime($app_name,$time){
		$conn=parent::getConn();
 		$sql="UPDATE app_root Set update_time=:update_time where app_name=:app_name ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->bindValue(":app_name",$app_name,PDO::PARAM_STR);
 			$st->bindValue(":update_time",$time,PDO::PARAM_STR);
 			$st->execute();
 			return true;
 		}catch(PDOException $e){
 			echo "updateAppRootUpdateTime():".$e->getMessage();
 			return false;
 		}			
	}
	
	public function updateAppRootIsScanning($app_name,$status){
		$conn=parent::getConn();
 		$sql="UPDATE app_root Set isscanning=:isscanning where app_name=:app_name ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->bindValue(":app_name",$app_name,PDO::PARAM_STR);
 			$st->bindValue(":isscanning",$status,PDO::PARAM_STR);
 			$st->execute();
 			return true;
 		}catch(PDOException $e){
 			echo "updateAppRootIsScanning():".$e->getMessage();
 			return false;
 		}			
	}

	
	/**
	 * 查询某应用层级
	 */
	public function selectAppLevel($app_name){
		$conn=parent::getConn();
 		$sql="select alias,level,ishide from level_alias where app_name='{$app_name}' order by level";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetchAll();
 			return $row;
 		}catch(PDOException $e){
 			echo "selectAppDirs():".$e->getMessage();
 			return false;
 		}			
	}

	public function insertAppLevel($app_name,$level,$alias){
 		$conn=parent::getConn();
 		$sql="insert into level_alias (level,alias,app_name) values(:level,:alias,:app_name) ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->bindValue(":level",$level,PDO::PARAM_INT);
 			$st->bindValue(":alias",$alias,PDO::PARAM_STR);
 			$st->bindValue(":app_name",$app_name,PDO::PARAM_STR);
 			
 			$st->execute();
 			return true;
 		}catch(PDOException $e){
 			echo "insertAppLevel() failed:".$e->getMessage();
 			return false;
 		}	
	}
	/**
	 * 查询要显示的二维码
	 */
	public function selectAppQrs($app_name){
		$conn=parent::getConn();
 		$sql="select id,app_path,data,create_time from qr where app_name='{$app_name}' order by create_time DESC limit 20";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetchAll();
 			return $row;
 		}catch(PDOException $e){
 			echo "selectAppDirs():".$e->getMessage();
 			return false;
 		}			
	}
	/**
	 * 查询二维码最大ID
	 */
	public function selectAppQrMaxId(){
		$conn=parent::getConn();
 		$sql="select MAX(id) from qr ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetch();
 			if(!$row[0]) return 1;
 			return $row[0];
 		}catch(PDOException $e){
 			echo "selectAppDirs():".$e->getMessage();
 			return false;
 		}			
	}
	public function insertAppQr($id,$app_name,$app_path,$data,$create_time){
 		$conn=parent::getConn();
 		$sql="insert into qr (id,app_name,app_path,data,create_time) values(:id,:app_name,:app_path,:data,:create_time) ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->bindValue(":id",$id,PDO::PARAM_INT);
 			$st->bindValue(":app_name",$app_name,PDO::PARAM_STR);
 			$st->bindValue(":app_path",$app_path,PDO::PARAM_STR);
 			$st->bindValue(":data",$data,PDO::PARAM_STR);
 			$st->bindValue(":create_time",$create_time,PDO::PARAM_STR);
 			$st->execute();
 			return true;
 		}catch(PDOException $e){
 			//echo "insertAppQr() failed:".$e->getMessage();
 			return false;
 		}	
	}
	/**
	 * 查询放弃扫描的文件夹
	 */
	public function selectAllScanDenies($app_name){
		$conn=parent::getConn();
 		$sql="select id,path from apps.scan_deny where app_name='{$app_name}' ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$rows=$st->fetchAll();
 			return $rows;
 		}catch(PDOException $e){
 			echo "selectAllScanDenies():".$e->getMessage();
 			return false;
 		}	
	}
	
	/**
	 * 根据各种条件查询二维码
	 */
	
	public function selectAllAppQrs($app_name){
		$conn=parent::getConn();
 		$sql="select id,app_path,data,create_time from qr where app_name='{$app_name}'  order by create_time DESC";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetchAll();
 			return $row;
 		}catch(PDOException $e){
 			echo "selectAppDirs():".$e->getMessage();
 			return false;
 		}	
	}
	public function selectAppQrsSEO($app_name,$app_apth){
		$conn=parent::getConn();
 		$sql="select id,app_path,data,create_time from qr where app_name='{$app_name}' AND app_path='{$app_apth}' order by create_time DESC";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetch();
 			return $row;
 		}catch(PDOException $e){
 			echo "selectAppDirs():".$e->getMessage();
 			return false;
 		}			
	}
	/**
	 * 查询某应用应用最新时间
	 */
	public function selectAppQrsCreatetime($app_name){
		$conn=parent::getConn();
 		$sql="select MAX(create_time) as max_create_time from qr where app_name='{$app_name}' ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetch();
 			return $row;
 		}catch(PDOException $e){
 			echo "selectAppQrsCount():".$e->getMessage();
 			return false;
 		}			
	}
	
	/**
	 * 查询某应用总数
	 */
	public function selectAppQrsCount($app_name){
		$conn=parent::getConn();
 		$sql="select COUNT(*) from qr where app_name='{$app_name}' ";
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetch();
 			return $row;
 		}catch(PDOException $e){
 			echo "selectAppQrsCount():".$e->getMessage();
 			return false;
 		}			
	}
	public function selectAppQrsById($id){
		$conn=parent::getConn();
 		$sql="select app_name,app_path,create_time from qr where id=".$id;
 		try{
 			$st=$conn->prepare($sql);
 			$st->execute();
 			$row=$st->fetch();
 			return $row;
 		}catch(PDOException $e){
 			echo "selectAppDirs():".$e->getMessage();
 			return false;
 		}			
	}
}
?>
