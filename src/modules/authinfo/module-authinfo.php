<?php

include_once "base/module.php";

class authinfo extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/$|", array($this, "getInfo"));
	}
	
	function getInfo(){
		return array("user" => $this->auth->GetUser(), "authlevel" => $this->auth->GetAuthLevel(), "groups" => $this->auth->GetGroups());
	}
} 