<?php

include_once "base/installer.php";

class Setup extends Installer {
	function Install(){
		$this->db->query('create table pages(
					  pageName varchar(50) primary key,
					  pageContent text not null
					);');
				   
		$this->db->query('insert into pages (pageName, pageContent) values("demo_page", "This is a demo page that was automatically added");');
		
		return true;
	}
	
	function Upgrade($oldversion){
		$this->Uninstall();
		$this->Install();
		
		return true;
	}
	
	function Uninstall(){
		$this->db->query('drop table pages;');
		return true;
	}
}