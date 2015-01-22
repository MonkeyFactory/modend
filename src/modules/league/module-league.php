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
		
		$route->register($this, "|^\/(\d*)\/reportmatch$|", array($this, "reportMatch"), "POST");
		$route->register($this, "|^\/(\d*)\/leaderboard$|", array($this, "getLeaderboard"));
		$route->register($this, "|^\/(\d*)\/scorehistory$|", array($this, "getScoreHistory"));
		
		$route->register($this, "|^\/(.*?)$|", array($this, "updateLeague"), "POST");
		$route->register($this, "|^\/(.*?)$|", array($this, "deleteLeague"), "DELETE");
		$route->register($this, "|^\/(.*?)$|", array($this, "getLeague"));
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
			
		return $sth->fetch(PDO::FETCH_ASSOC);
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
			
		$sth = $this->db->prepare("insert into leagues_matches values(null, ?, ?, ?, ?, ?);");
		if(!$sth->execute(array($leagueId, $input->MatchDate, $input->Player1, $input->Player2, $input->Winner)))
			throw new Exception("Error when inserting match into db");
			
		return true;
	}
	
	function getLeaderboard($input, $leagueId){
		if(!isset($leagueId) || $leagueId == "")
			throw new InvalidInputDataException("Argument leagueId is required");
			
		require_once "modules/authinfo/module-authinfo.php";
		$authModule = new authinfo($this->db, $this->auth);
			
		$players = array();
			
		$sth = $this->db->prepare("select * from leagues_matches where leagueId = ?");
		$sth->execute(array($leagueId));
		foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row){
			if(!array_key_exists($row["Player1"], $players))
				$players[$row["Player1"]] = array("wins" => 0, "draws" => 0, "playerId" => $row["Player1"]);
		
			if(!array_key_exists($row["Player2"], $players))
				$players[$row["Player2"]] = array("wins" => 0, "draws" => 0, "playerId" => $row["Player2"]);
		
			switch($row["Winner"]){
				case 0:
					//draw
					$players[$row["Player1"]]["draws"]++;
					$players[$row["Player2"]]["draws"]++;
				break;
				case 1:
					//Player1 wins
					$players[$row["Player1"]]["wins"]++;
				break;
				case 2:
					//Player2 wins
					$players[$row["Player2"]]["wins"]++;
				break;
				default:
					//Should not get here!
			}
		}
		
		$retval = array();
		foreach($players as $player){
			$score = $player["wins"] * 20 + $player["draws"] * 10;
			
			$retval[] = array("Name" => $authModule->lookupUserId("", $player["playerId"])["username"],
							  "Wins" => $player["wins"],
							  "Draws" => $player["draws"],
							  "Score" => $score);
		}
		
		usort($retval, function ($a, $b){
			if ($a["Score"] == $b["Score"]) {
				return 0;
			}
			return ($a["Score"] > $b["Score"]) ? -1 : 1;
		});
		
		return $retval;
	}
	
	
	
	function getScoreHistory($input, $leagueId){
		if(!isset($leagueId) || $leagueId == "")
			throw new InvalidInputDataException("Argument leagueId is required");
			
		require_once "modules/authinfo/module-authinfo.php";
		$authModule = new authinfo($this->db, $this->auth);
			
		$dates = array();
		$scoreHistory = array();
		$lastDate = null;
		$currentLevel = 0;
			
		$sth = $this->db->prepare("select * from leagues_matches where leagueId = ? order by MatchDate asc");
		$sth->execute(array($leagueId));
		foreach($sth->fetchAll(PDO::FETCH_ASSOC) as $row){
			$currentDate = date('d/m -y', strtotime($row["MatchDate"]));
		
			if($lastDate != $currentDate){
				//New date
				if($lastDate != null){
					//Players that didn't get any scores for this date should have their old score added again
					$this->padScore($scoreHistory, $currentLevel);
				}
				
				$dates[] = $currentDate;
				$lastDate = $currentDate;
				$currentLevel = count($dates) - 1;
			}
			
			//If the player didn't exist before add him and fill his scores with 0 up to current level
			if(!array_key_exists($row["Player1"], $scoreHistory)){
				$scoreHistory[$row["Player1"]] = array();
				
				for($i = 0; $i < $currentLevel;$i++){
					$scoreHistory[$row["Player1"]][] = 0;
				}
			}
		
			if(!array_key_exists($row["Player2"], $scoreHistory)){
				$scoreHistory[$row["Player2"]] = array();
				
				for($i = 0; $i < $currentLevel;$i++){
					$scoreHistory[$row["Player2"]][] = 0;
				}
			}
			
			switch($row["Winner"]){
				case 0:
					//draw
					$this->incrementScore($scoreHistory[$row["Player1"]], $currentLevel, 10);
					$this->incrementScore($scoreHistory[$row["Player2"]], $currentLevel, 10);
				break;
				case 1:
					//Player1 wins
					$this->incrementScore($scoreHistory[$row["Player1"]], $currentLevel, 20);
				break;
				case 2:
					//Player2 wins
					$this->incrementScore($scoreHistory[$row["Player2"]], $currentLevel, 20);
				break;
				default:
					//Should not get here!
			}
		}

		$this->padScore($scoreHistory, $currentLevel);
		
		array_unshift($dates, "");
		array_unshift($dates, count($dates));	
		$retval = array($dates);
		
		usort($scoreHistory, function ($a, $b){
			$k1 = $a[count($a)-1];
			$k2 = $b[count($b)-1];
		
			if ($k1 == $k2) {
				return 0;
			}
			return ($k1 > $k2) ? -1 : 1;
		});
		
		foreach($scoreHistory as $playerId => $player){
			array_unshift($player, 0);
			array_unshift($player, $authModule->lookupUserId("", $playerId)["username"]);
			$retval[] = $player;
		}
		
		return $retval;
	}
	
	function padScore(&$playerScores, $currentLevel){
		foreach($playerScores as &$player){
			$lastIndexScored = count($player) - 1;
			if($lastIndexScored == -1){
				$player[] = 0;
			}else{
				for($i = $lastIndexScored; $i < $currentLevel;$i++){
					$player[] = $player[$lastIndexScored];
				}
			}
		}
	}
	
	function incrementScore(&$player, $currentLevel, $score) {
		if($currentLevel == count($player) - 1){
			$player[$currentLevel] += $score;
		}else{
			if($currentLevel == 0){
				$previousScore = 0;
			}else{
				$previousScore = $player[$currentLevel - 1];
			}
			
			$player[] = $previousScore + $score;
		}
	}
} 