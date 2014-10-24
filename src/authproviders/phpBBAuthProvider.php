<?php

include "base/authProvider.php";
include_once "authmanager.php";

define('IN_PHPBB', true);
define('PHPBB_ROOT_PATH', '../../phpBB3/');

class phpBBAuthProvider extends AuthProvider {
	function __construct(){
		global $phpbb_root_path, $phpEx, $user, $db, $config, $cache, $template;
	
		$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
		$phpEx = substr(strrchr(__FILE__, '.'), 1);
		include($phpbb_root_path . 'common.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		
		// Start session management
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup();
		
		$this->user = $user;
	}

	function GetUser() {
		if($this->user->data['user_id'] == ANONYMOUS)
			return null;
	
		return $this->user->data['username_clean'];
	}
	
	function GetGroups() {
		if($this->user->data['user_id'] == ANONYMOUS)
			return array();
	
		$idToName = array(1 => "Guests", 2 => "Registered users", 3 => "COPPA User", 4 => "Global moderators", 5 => "Administrators", 6 => "Bots", 7 => "Newly Registered User");
		$groups = group_memberships(false, $this->user->data['user_id'], false);
		
		$retval = array();
		foreach($groups as $group){
			$retval[] = array("groupId" => $group["group_id"], "groupName" => array_key_exists($group["group_id"], $idToName) ? $idToName[$group["group_id"]] : "Unknown");
		}
		
		return $retval;
	}
	
	function HasGroup($groups, $groupname){
		foreach($groups as $group){
			if($group["groupName"] == $groupname)
				return true;
		}
		return false;
	}
	
	function GetAuthLevel() {
		if($this->user->data['user_id'] == ANONYMOUS)
			return NOT_LOGGED_IN;
		
		$groups = $this->GetGroups();
		
		if($this->HasGroup($groups, "Administrators"))
			return ADMIN;
		elseif ($this->HasGroup($groups, "Global moderators"))
			return MODERATOR;
		else
			return AUTHENTICATED_USER;
	}
}