<?php

include_once "base/module.php";
include_once "exceptions.php";

include_once "SimpleCRUD.php";

class events extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$this->crudEvents = new SimpleCRUD($this->db, $this->auth, "events");
		$this->crudEvents->RegisterRoutes($this, $route);
		$this->crudEvents->RegisterValidationFunction(array($this, "validateInputForEvent")); 
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
			
		if(!isset($input->EndDate) || preg_match("/\d[2-4]-\d[2]-\d[2] \d[2]:\d[2]/", $input->EndDate) === false){
			if(!isset($input->AllDayEvent)){
				throw new InvalidInputDataException("Field EndDate is required and has to be YYYY-MM-DD HH:MM. Or field AllDayEvent is required and has to be boolean");
			}else{
				$input->EndDate = null;
			}
		}else{
			$input->AllDayEvent = false;
		}		
	}
} 