<?php

include "db.php";
include "modulemanager.php";

function ModuleInDatabase($moduleName, $installed){
	for($i = 0; $i < count($installed);$i++){
		if($installed[$i][0] == $moduleName){
			$val = $installed[$i];
			unset($installed[$i]);
			return $val;
		}
	}
	
	return null;
}

$db = GetDatabaseConnection();
$moduleManager = new ModuleManager($db, null);

$installed = $moduleManager->GetInstalledModules();

echo "MODULE RESYNC STARTED! <br /><br />";

foreach($moduleManager->GetAvailableModules() as $module){
	echo "Found module $module <br />";
	
	$dbModInfo = ModuleInDatabase($module, $installed);
	if($dbModInfo == null){
		if($moduleManager->InstallModule($module)){
			echo "... Module was installed! <br />";
		}else{
			echo "... Module installation failed! <br />";
		}
	}else{
		try {
			$moduleManager->InstantiateModule($dbModInfo[0], $dbModInfo[1]);
			echo "... module installed and up to date <br/>";
		}
		catch(ModuleVersionMismatchException $ex){
			echo "... Version mismatch detected, upgrading <br />";
			
			if($moduleManager->UpgradeModule($module)){
				echo "...... Module was upgrade! <br />";
			}else{
				echo "...... Module upgrade failed! <br />";
			}
		}
	}
}

echo "<br />RESYNC COMPLETED!";