<?php

include_once "base/module.php";

class authinfo extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/$|", array($this, "getInfo"));
		$route->register($this, "|^/provoke/(\d)*$|", array($this, "provoke"));
		$route->register($this, "|^/userid/(\d)*$|", array($this, "lookupUserId"));
		$route->register($this, "|^/completeusername/(.*?)$|", array($this, "completeUsername"));
	}
	
	function provoke($input, $authlevel){
		AuthLevelOr403($this, $authlevel);
		return true;	
	}
	
	function getInfo(){
		return array("user" => $this->auth->GetUser(), "userId" => $this->auth->GetUserId(), "authlevel" => $this->auth->GetAuthLevel(), "groups" => $this->auth->GetGroups());
	}
	
	function PHPBBConnect(){
		include PHPBB_ROOT_PATH . "config.php";
		
		try{
			$dsn = "mysql:dbname=" . $dbname . ";host=" . $dbhost;
			return new PDO($dsn, $dbuser, $dbpasswd);
		}catch(PDOException $ex){
			throw new Exception("Unable to connect to PhpBB database: " . $ex->getMessage());
		}
	}
	
	function lookupUserId($input, $userId){
		include PHPBB_ROOT_PATH . "config.php";
	
		if(!isset($userId) || $userId == "")
			throw new InvalidInputDataException("Argument userId is required");
			
		$pdb = $this->PHPBBConnect();
		
		$sth = $pdb->prepare("select user_id, username from " . $table_prefix . "users where user_id = ? limit 1");
		if($sth->execute(array($userId)) == 0)
			throw new Exception("Failed to retrieve phpbb user from database");
			
		return $sth->fetch();
	}
	
	function completeUsername($input, $username){
		include PHPBB_ROOT_PATH . "config.php";
	
		if(!isset($username) || $username == "")
			throw new InvalidInputDataException("Argument username is required");
			
		$pdb = $this->PHPBBConnect();
		
		$sth = $pdb->prepare("select user_id, username from " . $table_prefix . "users where username like ?");
		if($sth->execute(array($username."%")) == 0)
			throw new Exception("Failed to retrieve phpbb users from database");
			
		return $sth->fetchAll();
	}
} 