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
	
	function validateInputForEvent($input){
		if(!isset($input->Title) || $input->Title == "")
			throw new InvalidInputDataException("Field Title is required");
			
		if(!isset($input->Description) || $input->Description == "")
			throw new InvalidInputDataException("Field Description is required");
			
		if(!isset($input->Location) || $input->Location == "")
			throw new InvalidInputDataException("Field Location is required");
			
		if(!isset($input->StartDate) || preg_match("/\d[2-4]-\d[2]-\d[2] \d[2]:\d[2]/", $input->StartDate) === false)
			throw new InvalidInputDataException("Field StartDate is required and has to be YYYY-MM-DD HH:MM");
			
		if(!isset($input->EndDate) || preg_match("/\d[2-4]-\d[2]-\d[2] \d[2]:\d[2]/", $input->EndDate) === false)
			throw new InvalidInputDataException("Field EndDate is required and has to be YYYY-MM-DD HH:MM");
			
		if(!isset($input->AllDayEvent))
			throw new InvalidInputDataException("Field AllDayEvent is required and has to be boolean");
	}
	
	function updateEvent($input, $eventId){
		AuthLevelOr403($this, MODERATOR);
		
		if(!isset($eventId) || $eventId == "")
			throw new InvalidInputDataException("Argument eventId is required");
		
		$this->validateInputForEvent($input);
			
		$sth = $this->db->prepare("update events set Title=?, Description=?, Location=?, StartDate=?, EndDate=?, AllDayEvent=? where eventId=? limit 1");
		if($sth->execute(array($input->Title, $input->Description, $input->Location, $input->StartDate, $input->EndDate, $input->AllDayEvent, $eventId)) == 0)
			throw new Exception("Database update failed when updating event");
			
		return $this->getEvent(null, $eventId);
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
		
		$this->validateInputForEvent($input);
		
		//Should add support for smart stuff like repeating events here...
		
		$sth = $this->db->prepare("insert into events values(0,?,?,?,?,?,?)");
		if($sth->execute(array($input->Title, $input->Location, $input->StartDate, $input->EndDate, $input->AllDayEvent, $input->Description)) == 0)
			throw new Exception("Database update failed when adding event");
			
		return array("eventId" => $this->db->lastInsertId(),"Title" => $input->Title, "Location" => $input->Location, "Description" => $input->Description, "StartDate" => $input->StartDate, "AllDayEvent" => $input->AllDayEvent, "EndDate" => $input->EndDate);
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