<?php

include_once "base/module.php";
include_once "exceptions.php";

class gallery extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){

	}
	
} 