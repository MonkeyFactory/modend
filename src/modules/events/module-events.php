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
	
		if(!isset($eventId) || $eventId == "")
			throw new InvalidInputDataException("Argument eventId is required");
			
		$sth = $this->db->prepare("delete from events where eventId = ? limit 1;");
		if($sth->execute(array($eventId)) == 0)
			throw new Exception("Database update failed when deleting event");

		return true;
	}	
	
	function addEvent($input){
		AuthLevelOr403($this, MODERATOR);
	
		
	}
	
	function listEvents(){
		return $this->db->query("select * from events", PDO::FETCH_ASSOC)->fetchAll();
	}
	
	function getEvent($input, $eventId){
		$sth = $this->db->prepare("select * from events where eventId = ? limit 1;");
		$sth->execute(array($eventId));

		if(!$event = $sth->fetch(PDO::FETCH_ASSOC))
			throw new NoSuchResourceException();
		else
			return $event;
	}
} 