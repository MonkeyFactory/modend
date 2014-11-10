<?php

include "routeengine.php";
include "modulemanager.php";
include "db.php";
include "authmanager.php";
include_once "config.php";

class Core {
	function __construct(){
		$this->db = GetDatabaseConnection();
		$this->auth = new AuthManager(AUTHPROVIDERNAME);
		$this->moduleManager = new ModuleManager($this->db, $this->auth);
		$this->route = new RouteEngine();
	}
	
	function BuildRoutes(){
		foreach($this->moduleManager->GetInstalledModules() as $module){
			try{
				$moduleInstance = $this->moduleManager->InstantiateModule($module[0], $module[1]);
				$moduleInstance->Init();
				$moduleInstance->RegisterRoutes($this->route);
			}catch(ModuleVersionMismatchException $ex){
				//Version missmatch detected, do not include the module
				if(isset($_REQUEST["debug"])){
					echo "Notice: " . $ex->GetMessage() . ". Not instantiating!";
				}
			}
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