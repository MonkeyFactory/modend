<?php

abstract class AuthProvider {
	abstract function GetUser();
	
	abstract function GetUserId();
	
	abstract function GetGroups();
	
	abstract function GetAuthLevel();
}