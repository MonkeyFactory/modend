<?php

include "routeengine.php";
include "modulemanager.php";
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
	
		$this->moduleManager = new ModuleManager($this->db);
		$this->route = new RouteEngine();
	}
	
	function BuildRoutes(){
		foreach($this->moduleManager->GetInstalledModules() as $module){
			$moduleInstance = $this->moduleManager->InstantiateModule($module[0], $module[1])[0];
			$moduleInstance->RegisterRoutes($this->route);
		}
	}
	
	function ProcessRequest($module, $query){
		$result = $this->route->Invoke($module, $query);
		return json_encode($result);
	}
}