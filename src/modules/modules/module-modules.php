<?php

include_once "base/module.php";

function ModuleIsInstalled($moduleName, $installed){
	for($i = 0; $i < count($installed);$i++){
		if($installed[$i][0] == $moduleName){
			$val = $installed[$i];
			return $val;
		}
	}
	
	return null;
}

class modules extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^/$|", array($this, "getModules"));
		$route->register($this, "|^/install/(.*?)$|", array($this, "installModule"));
		$route->register($this, "|^/upgrade/(.*?)$|", array($this, "upgradeModule"));
		$route->register($this, "|^/uninstall/(.*?)$|", array($this, "uninstallModule"));
	}
	
	function installModule($input, $moduleName){
		return true;
	}
	
	function upgradeModule($input, $moduleName){
		return true;
	}
	
	function uninstallModule($input, $moduleName){
		return true;
	}
	
	function getModules(){
		$moduleManager = new ModuleManager($this->db, $this->auth);
		
		$allModules = $moduleManager->GetAvailableModules();
		$installedModules = $moduleManager->GetInstalledModules();
		
		$retVal = array();
		foreach($allModules as $module){
			$moduleItem = array("moduleName" => $module, "dbVersion" => null, "fsVersion" => null, "installed" => false);
			
			$dbModInfo = ModuleIsInstalled($module, $installedModules);
			try {
				if($dbModInfo == null){
					$moduleManager->InstantiateModule($module, 0);
				}else{
					$moduleItem["installed"] = true;
					$moduleInstance = $moduleManager->InstantiateModule($dbModInfo[0], $dbModInfo[1]);
					
					$moduleItem["dbVersion"] = $moduleInstance->version;
					$moduleItem["fsVersion"] = $moduleInstance->version;;
				}
			}
			catch(ModuleVersionMismatchException $ex){
					$moduleItem["dbVersion"] = $ex->currentVersion;
					$moduleItem["fsVersion"] = $ex->expectedVersion;
			}
			
			$retVal[] = $moduleItem;
		}
		
		return $retVal;
	}
} 