<?php

class ModuleManager {
	function __construct($db){
		$this->db = $db;
	}
	
	function GetInstalledModules(){
		$retval = array();
		$sql = 'select * from modules';
		foreach($this->db->query($sql) as $r){
			$retval[] = array($r["moduleName"], $r["installedVersion"]);
		}
		
		return $retval;
	}
}