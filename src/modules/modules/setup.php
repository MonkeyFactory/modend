<?php

include_once "base/installer.php";

class setup_modules extends Installer {
	function Install(){
		return true;
	}
	
	function Upgrade($oldversion){
		return true;
	}
	
	function Uninstall(){
		return true;
	}
}