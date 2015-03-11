<?php

include_once "base/installer.php";

class setup_league extends Installer {
	function Install(){
		$this->db->query('create table leagues(
					  leagueId int auto_increment primary key,
					  Name varchar(50) not null,
					  Description text not null,
					  StartDate datetime not null,
					  EndDate datetime null
					);');
				   
		$this->db->query('insert into leagues  (Name, Description, StartDate) values("Demo League", "This is a demo league that was automatically added", now());');
		
		$this->db->query('create table leagues_matches(
					matchId int auto_increment primary key,
					leagueId int not null,
					MatchDate datetime not null,
					Player1 int not null,
					Player2 int not null,
					Winner int null,
					Player1PassedChallenge bool default FALSE,
					Player2PassedChallenge bool default FALSE);');
		
		$this->db->query('create table leagues_challenges(
						challengeId int auto_increment primary key,
						leagueId int not null,
						Description varchar(255) not null,
						WeekNumber int not null
						);');
		
		return true;
	}
	
	function Upgrade($oldversion){
		if($oldversion == 1.0){
			$this->db->query('create table leagues_challenges(
						challengeId int auto_increment primary key,
						leagueId int not null,
						Description varchar(255) not null,
						WeekNumber int not null
						);');
						
			$this->db->query('alter table leagues_matches
							  add column Player1PassedChallenge bool default FALSE');
							  
			$this->db->query('alter table leagues_matches
							  add column Player2PassedChallenge bool default FALSE');
		}
	
		return true;
	}
	
	function Uninstall(){
		$this->db->query('drop table leagues;');
		return true;
	}
}