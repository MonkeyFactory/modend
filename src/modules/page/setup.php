<?php

include_once "base/installer.php";

class Setup extends Installer {
	function Install(){
		$this->db->query('create table pages(
					  pageId int auto_increment primary key,
					  pageTitle varchar(50) not null,
					  pageContent text not null
					);');
				   
		$this->db->query('insert into pages (pageTitle, pageContent) values("Demo Page", "This is a demo page that was automatically added");');
		
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