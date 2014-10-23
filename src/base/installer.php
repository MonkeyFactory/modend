<?php

abstract class Installer {
	function __construct($db){
		$this->db = $db;
	}
	
	function Install(){
		return false;
	}
	
	function Upgrade($oldversion){
		return false;
	}
	
	function Uninstall(){
		return false;
	}
}