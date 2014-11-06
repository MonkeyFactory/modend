<?php

include_once "base/installer.php";

class setup_events extends Installer {
	function Install(){
		$this->db->exec("create table events(
							eventId int auto_increment primary key,
							Title varchar(50) not null,
							Location varchar(50) not null,
							StartDate datetime not null,
							EndDate datetime not null,
							Description text
						);");
		
		$this->db->exec("insert into events values (0, 'Test Event', 'Some place more familiar', now(), date_add(now(), interval 2 hour), 'Test event automagically added by the setup script');");
		
		return true;
	}
	
	function Upgrade($oldversion){
		$this->Uninstall();
		$this->Install();
		
		return true;
	}
	
	function Uninstall(){
		$this->db->exec("drop table if exists events;");
		return true;
	}
}