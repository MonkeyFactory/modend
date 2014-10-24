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
	
		$groups = group_memberships(false, $this->user->data['user_id'], false);
		echo $groups;
		
	}
	
	function GetAuthLevel() {
		if($this->user->data['user_id'] == ANONYMOUS)
			return NOT_LOGGED_IN;
	
		
		return ADMIN;
	}
}