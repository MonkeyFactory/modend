<?php

include "base/authProvider.php";
include_once "authmanager.php";


class dummyAuthProvider extends AuthProvider {
	function __construct(){
	
	}

	function GetUser() {
		return "Admin";
	}
	
	function GetUserId() {
		return 1;
	}
	
	function GetGroups() {
		return array("groupId" => 1, "groupName" => "Admins");
	}
	
	function HasGroup($groups, $groupname){
		return True;
	}
	
	function GetAuthLevel() {
		return ADMIN;
	}
}