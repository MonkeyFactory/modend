<?php

include "routeengine.php";
include "config.php";

class Core {
	function __construct(){
		global $dbhost, $dbname, $dbuser, $dbpass;
	
		try{
			$dsn = "mysql:dbname=$dbname;host=$dbhost";
			$this->db = new PDO($dsn, $dbuser, $dbpass);
		}catch(PDOException $ex){
			http_response_code(500);
			die("Check database connection: " . $ex->getMessage());
		}
	
		$this->modules = array("page");
		$this->route = new RouteEngine();
	}
	
	function BuildRoutes(){
		foreach($this->modules as $module){
			include "modules/$module/module-$module.php";
			$moduleInstance = new $module($db);
			$moduleInstance->RegisterRoutes($this->route);
		}
	}
	
	function ProcessRequest($module, $query){
		$result = $this->route->Invoke($module, $query);
		return json_encode($result);
	}
}