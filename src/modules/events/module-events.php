<?php

include_once "base/module.php";
include_once "exceptions.php";

class events extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/$|", array($this, "addEvent"), "POST");
		$route->register($this, "|^\/$|", array($this, "listEvents"));
		$route->register($this, "|^\/(\d*)$|", array($this, "updateEvent"), "POST");
		$route->register($this, "|^\/(\d*)$|", array($this, "deleteEvent"), "DELETE");
		$route->register($this, "|^\/(\d*)$|", array($this, "getEvent"));
	}
	
	function updateEvent($input, $eventId){
		AuthLevelOr403($this, MODERATOR);
		
	}
	
	function deleteEvent($input, $eventId){
		AuthLevelOr403($this, MODERATOR);
	
	}
	
	function addEvent($input){
		AuthLevelOr403($this, MODERATOR);
	
		
	}
	
	function listEvents(){

	}
	
	function getEvent($input, $eventId){
		
	}
} 