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
		$route->register($this, "|^\/(\d*)$|", array($this, "getGallery"));
	}

	//GALLERY//
	
	function addGallery($input){
		
	}
	
	function updateGallery($input, $galleryId){
	
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
	
	//IMAGES//
	
	function uploadImage($input){
	
	}
	
	function removeImage($input, $imageId){
	
	}
	
	function linkImage($input, $imageId, $galleryId){
	
	}
	
	function unlinkImage($input, $imageId, $galleryId){
	
	}
} 