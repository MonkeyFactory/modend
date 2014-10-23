<?php

abstract class AuthProvider {
	abstract function GetUser();
	
	abstract function GetGroups();
	
	abstract function GetAuthLevel();
}