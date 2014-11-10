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
	}

	//GALLERY//
	
	function addGallery($input){
		
	}
	
	function updateGallery($input, $galleryId){
	
	}
	
	function listGalleries(){
	
	}
	
	function getGallery($input, $galleryId){
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