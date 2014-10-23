<?php

include "routeengine.php";
include "modulemanager.php";
include "db.php";

class Core {
	function __construct(){
		$this->db = GetDatabaseConnection();
		$this->moduleManager = new ModuleManager($this->db);
		$this->route = new RouteEngine();
	}
	
	function BuildRoutes(){
		foreach($this->moduleManager->GetInstalledModules() as $module){
			$moduleInstance = $this->moduleManager->InstantiateModule($module[0], $module[1]);
			$moduleInstance->RegisterRoutes($this->route);
		}
	}
	
	function ProcessRequest($module, $query){
		$result = $this->route->Invoke($module, $query);
		return json_encode($result);
	}
}