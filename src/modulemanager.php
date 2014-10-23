<?php

include_once "exceptions.php";

class ModuleManager {
	function __construct($db, $auth){
		$this->db = $db;
		$this->auth = $auth;
	}
	
	function GetAvailableModules(){
		$modules = array();
		foreach(scandir("modules/") as $f){
			if($f != "." && $f != ".." && is_dir("modules/$f") && file_exists("modules/$f/module-$f.php")){
				$modules[] = $f;
			}
		}
		
		return $modules;
	}
	
	function InstantiateModule($moduleName, $expectedVersion){
		$modulePath = "modules/$moduleName/module-$moduleName.php";
		if(!file_exists($modulePath)){
			throw new ModuleNotFoundException("No file at: $modulePath");
		}
			
		include_once $modulePath;
		$module = new $moduleName($this->db, $this->auth);
		
		if($module->version == $expectedVersion){
			return $module;
		}else{
			throw new ModuleVersionMismatchException("Module '$moduleName' is at version {$module->version} but the database has it at $expectedVersion", $module->version, $expectedVersion);
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
			
		$modulePath = "modules/$moduleName/module-$moduleName.php";
		if(!file_exists($modulePath)){
			throw new ModuleNotFoundException("No file at: $modulePath");
		}
			
		include_once $setupPath;
		include_once $modulePath;
		
		return array(new Setup($this->db), (new $moduleName($this->db))->version);
	}
	
	function InstallModule($moduleName){
		$setup = $this->GetModuleSetupObject($moduleName);
		if($setup[0]->Install()){
			$this->db->query("insert into modules (moduleName, installedVersion) values('$moduleName', '{$setup[1]}');");
			return true;
		}
		
		return false;
	}
	
	function UpgradeModule($moduleName, $oldversion){
		$setup = $this->GetModuleSetupObject($moduleName);
		if($setup[0]->Upgrade($oldversion)){
			return $this->db->exec("update modules set installedVersion = '{$setup[1]}' where moduleName = '$moduleName' limit 1;") > 0;
		}
		
		return false;
	}
	
	function UninstallModule($moduleName){
		$setup = $this->GetModuleSetupObject($moduleName);
		if($setup[0]->Uninstall()){
			return $this->db->exec("delete from modules where moduleName = '$moduleName' limit 1;") > 0;
		}
		
		return false;
	}
}