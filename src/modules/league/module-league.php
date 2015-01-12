<?php

include_once "base/module.php";
include_once "exceptions.php";

class league extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/$|", array($this, "addLeague"), "POST");
		$route->register($this, "|^\/$|", array($this, "listLeagues"));
		$route->register($this, "|^\/(.*?)$|", array($this, "updateLeague"), "POST");
		$route->register($this, "|^\/(.*?)$|", array($this, "deleteLeague"), "DELETE");
		$route->register($this, "|^\/(.*?)$|", array($this, "getLeague"));
		
		$route->register($this, "|^\/(.*?)\/reportmatch$|", array($this, "reportMatch"), "POST");
		$route->register($this, "|^\/(.*?)\/leaderboard$|", array($this, "getLeaderboard"));
		$route->register($this, "|^\/(.*?)\/scorehistory$|", array($this, "getScoreHistory"));
	}
	
	/* LEAGUE MANAGMENT */
	
	function validateInputForLeague($input){
		if(!isset($input->Name) || $input->Name == "")
			throw new InvalidInputDataException("Field Name is required");
			
		if(!isset($input->Description) || $input->Description == "")
			throw new InvalidInputDataException("Field Description is required");
			
		if(!isset($input->StartDate) || preg_match("/\d[2-4]-\d[2]-\d[2] \d[2]:\d[2]/", $input->StartDate) === false)
			throw new InvalidInputDataException("Field StartDate is required and has to be YYYY-MM-DD HH:MM");
	}
	
	function addLeague($input){
		AuthLevelOr403($this, MODERATOR);
		
		$this->validateInputForLeague($input);
		
		$sth = $this->db->prepare("insert into league values(null, ?, ?, ?, ?))");
		if($sth->execute(array($input->Name, $input->Description, $input->StartDate, $input->EndDate)) == 0)
			throw new Exception("Database update failed when creating league");
			
		return $this->getLeague($this->db->lastInsertId());
	}
	
	function updateLeague($input, $leagueId){
		AuthLevelOr403($this, MODERATOR);
		
		if(!isset($leagueId) || $leagueId == "")
			throw new InvalidInputDataException("Argument leagueId is required");
		
		$this->validateInputForLeague($input);
		
		$sth = $this->db->prepare("update league set Name = ?, Description = ?, StartDate = ?, EndDate = ? where leagueId = ? limit 1;");
		if($sth->execute(array($input->Name, $input->Description, $input->StartDate, $input->EndDate, $leagueId)) == 0)
			throw new Exception("Database update failed when updating league");

		return $this->getLeague($leagueId);
	}
	
	function deleteLeague($input, $leagueId){
		AuthLevelOr403($this, MODERATOR);
		
		if(!isset($leagueId) || $leagueId == "")
			throw new InvalidInputDataException("Argument leagueId is required");
		
		$sth = $this->db->prepare("delete from leagues where leagueId = ? limit 1;");
		if($sth->execute(array($leagueId)) == 0)
			throw new Exception("Database update failed when deleting league");

		return true;
	}
		
	function listLeagues(){
		return $this->db->query("select * from leagues", PDO::FETCH_ASSOC)->fetchAll();
	}
	
	function getLeague($input, $leagueId){
		if(!isset($leagueId) || $leagueId == "")
			throw new InvalidInputDataException("Argument leagueId is required");
			
		$sth = $this->db->prepare("select * from leagues where leagueId=? limit 1");
		if($sth->execute(array($leagueId)) == 0)
			throw new Exception("Failed to retrieve league from database");
			
		return $sth->fetch();
	}
	
	/* SCORE MANAGMENT */
	
	function reportMatch($input, $leagueId){
		AuthLevelOr403($this, REGISTERED_USER);
			
		if(!isset($leagueId) || $leagueId == "")
			throw new InvalidInputDataException("Argument leagueId is required");
			
		if(!isset($input->MatchDate) || preg_match("/\d[2-4]-\d[2]-\d[2] \d[2]:\d[2]/", $input->MatchDate) === false)
			throw new InvalidInputDataException("Field MatchDate is required and has to be YYYY-MM-DD HH:MM");
			
		if(!isset($input->Player1) || $input->Player1 == "")
			throw new InvalidInputDataException("Argument Player1 is required and must be int");
			
		if(!isset($input->Player2) || $input->Player2 == "")
			throw new InvalidInputDataException("Argument Player2 is required and must be int");
			
		if(!isset($input->Winner) || $input->Winner == "" || $input->Winner > 2 || $input->Winner < 0)
			throw new InvalidInputDataException("Argument Winner is required, must be int and between 0 and 2");
			
		$sth = $this->db->prepare("insert into leagues_matches values(null, ?, ?, ?, ?);");
		if(!$sth->execute(array($leagueId, $input->MatchDate, $input->Player1, $input->Player2, $input->Winner)))
			throw new Exception("Error when inserting match into db");
			
		return true;
	}
	
	function getLeaderboard($input, $leagueId){
		
	}
	
	function getScoreHistory($input, $leagueId){
		
	}
} 