<?php

include "base/installer.php";

class Setup extends Installer {
	function Install(){
		$this->db->query('create table pages(
					  pageId int auto_increment primary key,
					  pageTitle varchar(50) not null,
					  pageContent text not null
					);');
				   
		return true;
	}
	
	function Upgrade($oldversion){
		return true;
	}
	
	function Uninstall(){
		$this->db->query('drop table pages;');
		return true;
	}
}