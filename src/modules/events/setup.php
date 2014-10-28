<?php

include_once "base/installer.php";

class setup_events extends Installer {
	function Install(){
		
		
		return true;
	}
	
	function Upgrade($oldversion){
		$this->Uninstall();
		$this->Install();
		
		return true;
	}
	
	function Uninstall(){
		
		return true;
	}
}