<?php

include_once "exceptions.php";

class ModuleManager {
	function __construct($db){
		$this->db = $db;
	}
	
	function GetAvailableModules(){
		
	}
	
	function InstantiateModule($moduleName, $expectedVersion){
		$modulePath = "modules/$moduleName/module-$moduleName.php";
		if(!file_exists($modulePath)){
			throw new ModuleNotFoundException("No file at: $modulePath");
		}
			
		include $modulePath;
		if($version == $expectedVersion){
			return array(new $moduleName($db), $version);
		}else{
			throw new ModuleVersionMismatchException("Module '$moduleName' is at version $version but the database has it at $expectedVersion", $version, $expectedVersion);
		}
	}
	
	function GetInstalledModules(){
		$retval = array();
		$sql = 'select * from modules';
		foreach($this->db->query($sql) as $r){
			$retval[] = array($r["moduleName"], $r["installedVersion"]);
		}
		
		return $retval;
	}
	
	function GetModuleSetupObject($moduleName){
		$setupPath = "modules/$moduleName/setup.php";
		if(!file_exists($setupPath))
			throw new ModuleHasNoSetupException("Module $moduleName has no setup file");
			
		include $setupPath;
		return new Setup($this->db);
	}
	
	function InstallModule($moduleName){
		$setup = $this->GetModuleSetupObject($moduleName);
		return $setup->Install();
	}
	
	function UpgradeModule($moduleName, $oldversion){
		$setup = $this->GetModuleSetupObject($moduleName);
		return $setup->Upgrade($oldversion);
	}
	
	function UninstallModule($moduleName){
		$setup = $this->GetModuleSetupObject($moduleName);
		return $setup->Uninstall();
	}
}