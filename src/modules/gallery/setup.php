<?php

include_once "base/installer.php";

define("GALLERY_IMAGE_PATH", "imagegallery/");

class setup_gallery extends Installer {
	function Install(){
		$this->db->exec("create table imagegalleries (GalleryId int auto_increment primary key, 
													  Title varchar(50) not null,
													  Owner int not null
													  );");
													  
		$this->db->exec("create table imagelinking (LinkId int auto_increment primary key, 
													GalleryId int not null, 
													ImageId int not null);");
		
		$this->db->exec("create table images (ImageId int auto_increment primary key,
											  Filename varchar(50) not null,
											  Description varchar(250) null,
											  Added datetime not null
											  );");
																
		if(!file_exists(GALLERY_IMAGE_PATH)){
			mkdir(GALLERY_IMAGE_PATH);
		}
									
		$this->db->exec("insert into imagegalleries values (0, 'Test Gallery', 2)");
		$this->db->exec("insert into images values (0, 'test.jpg', 'Test bild', now())");
		$this->db->exec("insert into imagelinking values (1,1)");
					
		return true;
	}
	
	function Upgrade($oldversion){
		$this->Uninstall();
		$this->Install();
		
		return true;
	}
	
	function Uninstall(){
		$this->db->exec("drop table imagelinking;");
		$this->db->exec("drop table imagegalleries;");
		$this->db->exec("drop table images;");
		
		return true;
	}
}