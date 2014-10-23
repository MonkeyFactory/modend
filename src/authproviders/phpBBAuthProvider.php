<?php

include "base/authProvider.php";
include_once "authmanager.php";

class phpBBAuthProvider extends AuthProvider {
	function __construct(){
		
	}

	function GetUser() {
		return "nojan";
	}
	
	function GetGroups() {
		return "admins";
	}
	
	function GetAuthLevel() {
		return ADMIN;
	}
}