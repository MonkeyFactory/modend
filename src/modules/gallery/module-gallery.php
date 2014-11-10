<?php

include_once "base/module.php";
include_once "exceptions.php";

class gallery extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/$|", array($this, "listGalleries"));
		$route->register($this, "|^\/$|", array($this, "addGallery"), "POST");
		$route->register($this, "|^\/(\d*)$|", array($this, "getGallery"));
		$route->register($this, "|^\/(\d*)$|", array($this, "updateGallery"), "POST");
		$route->register($this, "|^\/(\d*)$|", array($this, "deleteGallery"), "DELETE");
	}

	//GALLERY//
	
	function addGallery($input){
		AuthLevelOr403($this, MODERATOR);
	
		if(!isset($input->Title) || $input->Title == "")
			throw new InvalidInputDataException("Field Title is required");
			
		$sth = $this->db->prepare("insert into imagegalleries values(0,?,?);");
		if($sth->execute(array($input->Title, $this->auth->GetUserId())) == 0)
			throw new Exception("Database update failed when adding gallery");
			
		return $this->getGallery(null, $this->db->lastInsertId());
	}
	
	function updateGallery($input, $galleryId){
		AuthLevelOr403($this, MODERATOR);
	
		if(!isset($galleryId) || $galleryId == "")
			throw new InvalidInputDataException("Argument galleryId is required");
		
		if(!isset($input->Title) || $input->Title == "")
			throw new InvalidInputDataException("Field Title is required");

		$sth = $this->db->prepare("update imagegalleries set Title=? where GalleryId=? limit 1");
		if($sth->execute(array($input->Title, $galleryId)) == 0)
			throw new Exception("Database update failed when updating gallery");
			
		return $this->getGallery(null, $galleryId);
	}
	
	function listGalleries(){
		return $this->db->query("select * from imagegalleries", PDO::FETCH_ASSOC)->fetchAll();
	}
	
	function getGallery($input, $galleryId){
		$sth1 = $this->db->prepare("select * from imagegalleries where GalleryId=?");
		$sth1->execute(array($galleryId));
		$retval = $sth1->fetch(PDO::FETCH_ASSOC);
		
		$sth2 = $this->db->prepare("select i.* from images as i inner join imagelinking as l on l.ImageId = i.ImageId where l.GalleryId=?");
		$sth2->execute(array($galleryId));
		$retval["images"] = $sth2->fetchAll(PDO::FETCH_ASSOC);
		
		return $retval;
	}
	
	function deleteGallery($input, $galleryId){
		AuthLevelOr403($this, MODERATOR);
		
		if(!isset($galleryId) || $galleryId == "")
			throw new InvalidInputDataException("Argument galleryId is required");
			
		$sth1 = $this->db->prepare("delete from imagelinking where GalleryId=?;");
		$sth2 = $this->db->prepare("delete from imagegalleries where GalleryId=? limit 1;");
		
		$sth1->execute(array($galleryId));
		$sth2->execute(array($galleryId));
		
		return true;
	}
	
	//IMAGES//
	
	function uploadImage($input){
		AuthLevelOr403($this, MODERATOR);
	
	}
	
	function removeImage($input, $imageId){
		AuthLevelOr403($this, MODERATOR);
	
	}
	
	function linkImage($input, $imageId, $galleryId){
		AuthLevelOr403($this, MODERATOR);
	
	}
	
	function unlinkImage($input, $imageId, $galleryId){
		AuthLevelOr403($this, MODERATOR);
	
	}
} 