<?php

include_once "base/module.php";
include "pagemodel.php";
include_once "exceptions.php";

class page extends Module {
	function SetMetadata(){
		$this->version = 1.1;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/$|", array($this, "addPage"), "POST");
		$route->register($this, "|^\/$|", array($this, "listPages"));
		$route->register($this, "|^\/(\d*)$|", array($this, "getPage"));
	}
	
	function addPage($input){
		AuthLevelOr403($this, MODERATOR);
	
		if(!isset($input->pageTitle) || $input->pageTitle == ""){
			throw new InvalidInputDataException("Argument pageTitle is required");
		}
		
		if(!isset($input->pageContent) || $input->pageContent == ""){
			throw new InvalidInputDataException("Argument pageContent is required");
		}
		
		$sth = $this->db->prepare("insert into pages (pageTitle, pageContent) values(?, ?);");
		if($sth->execute(array($input->pageTitle, $input->pageContent)) == 0)
			throw new Exception("Database insert failed when adding page");

		return array("pageTitle" => $input->pageTitle, "pageContent" => $input->pageContent, "pageId" => $this->db->lastInsertId());
	}
	
	function listPages(){
		$retval = array();
		
		foreach($this->db->query("select * from pages", PDO::FETCH_ASSOC) as $row){
			$retval[] = $row;
		}
		
		return $retval;
	}
	
	function getPage($input, $pageId){
		$sth = $this->db->prepare("select pageTitle, pageContent from pages where pageId = ?");
		$sth->execute(array($pageId));
		
		$page = $sth->fetch(PDO::FETCH_ASSOC);
		if(!$page)
			throw new NoSuchResourceException();
		else 
			return $page;
	}
} 