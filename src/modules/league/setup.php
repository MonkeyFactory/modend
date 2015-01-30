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
					Winner int null);');
		
		return true;
	}
	
	function Upgrade($oldversion){
		return true;
	}
	
	function Uninstall(){
		$this->db->query('drop table leagues;');
		return true;
	}
}