<?php

include "routeengine.php";

class Core {
	function __construct(){
		$this->modules = array("page");
		$this->route = new RouteEngine();
	}
	
	function BuildRoutes(){
		foreach($this->modules as $module){
			include "modules/$module/module-$module.php";
			$moduleInstance = new $module();
			$moduleInstance->RegisterRoutes($this->route);
		}
	}
	
	function ProcessRequest($module, $query){
		$result = $this->route->Invoke($module, $query);
		return json_encode($result);
	}
}