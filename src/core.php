<?php

include "routeengine.php";
include "modulemanager.php";
include "db.php";
include "authmanager.php";
include_once "config.php";

class Core {
	function __construct(){
		global $authprovidername;
	
		$this->db = GetDatabaseConnection();
		$this->auth = new AuthManager($authprovidername);
		$this->moduleManager = new ModuleManager($this->db, $this->auth);
		$this->route = new RouteEngine();
	}
	
	function BuildRoutes(){
		foreach($this->moduleManager->GetInstalledModules() as $module){
			$moduleInstance = $this->moduleManager->InstantiateModule($module[0], $module[1]);
			$moduleInstance->Init();
			$moduleInstance->RegisterRoutes($this->route);
		}
	}
	
	function ProcessRequest($module, $query){
		try{
			$rawInput = file_get_contents("php://input");
			$input = json_decode($rawInput);
		}catch(Exception $ex){
			$input = array();
		}
	
		$result = $this->route->Invoke($module, $query, $input);
		return json_encode($result);
	}
}