<?php

define("NOT_LOGGED_IN", 0);
define("AUTHENTICATED_USER", 1);
define("MODERATOR", 2);
define("ADMIN", 3);

function AuthLevelOr403($module, $requiredLevel){
	if($requiredLevel == NOT_LOGGED_IN){
		return;
	}

	$authlevel = $module->auth->GetAuthLevel();
	if($authlevel == NOT_LOGGED_IN){
		http_response_code(401);
		exit;
	}
	elseif($authlevel < $requiredLevel){
		http_response_code(403);
		exit;
	}
}

class AuthManager {
	function __construct($providerName){
		include_once "authproviders/$providerName.php";
		$this->provider = new $providerName();
	
		/*$this->providers = array();
		foreach(glob("authproviders/*.php") as $provider){
			include_once "authproviders/$provider";
			
			$providerName = explode(".", $provider)[0];
			$this->providers[] = new $providerName();
		}*/
	}
	
	function GetUser(){
		return $this->provider->GetUser();
	}
	
	function GetUserId(){
		return $this->provider->GetUserId();
	}
	
	function IsLoggedIn(){
		return $this->GetAuthLevel() > NOT_LOGGED_IN;
	}
	
	function GetAuthLevel(){
		return $this->provider->GetAuthLevel();
	}
	
	function GetGroups(){
		return $this->provider->GetGroups();
	}
	
	function AuthByGroupMembership($groupName){
		$groups = $this->provider.GetGroups();
		
		foreach($groups as $group){
			if($group["groupName"] == $groupName)
				return true;
		}
		return false;
	}
}