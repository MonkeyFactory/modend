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
			$modulePath = "modules/{$module[0]}/module-{$module[0]}.php";
			if(!file_exists($modulePath)){
				throw new ModuleNotFoundException("No file at: $modulePath");
			}
			
			include $modulePath;
			if($module[1] == $version){
				$moduleInstance = new $module[0]($db);
				$moduleInstance->RegisterRoutes($this->route);
			}else{
				throw new ModuleVersionMismatchException("Module '{$module[0]}' is at version $version but the database has it at {$module[1]}");
			}
		}
	}
	
	function ProcessRequest($module, $query){
		$result = $this->route->Invoke($module, $query);
		return json_encode($result);
	}
}