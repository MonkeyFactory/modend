<?php

include_once "base/module.php";
include_once "exceptions.php";

class page extends Module {
	function SetMetadata(){
		$this->version = 1.0;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/$|", array($this, "addPage"), "POST");
		$route->register($this, "|^\/$|", array($this, "listPages"));
		$route->register($this, "|^\/(.*?)$|", array($this, "updatePage"), "POST");
		$route->register($this, "|^\/(.*?)$|", array($this, "getPage"));
	}
	
	function updatePage($input, $pageName){
		AuthLevelOr403($this, MODERATOR);
		
		if(!isset($input->pageContent) || $input->pageContent == ""){
			throw new InvalidInputDataException("Argument pageContent is required");
		}
		
		$sth = $this->db->prepare("update pages set pageContent = ? where pageName = ? limit 1;");
		if($sth->execute(array($input->pageContent, $input->pageName)) == 0)
			throw new Exception("Database update failed when updating page");

		return array("pageName" => $input->pageName, "pageContent" => $input->pageContent);
	}
	
	function addPage($input){
		AuthLevelOr403($this, MODERATOR);
	
		if(!isset($input->pageName) || $input->pageName == ""){
			throw new InvalidInputDataException("Argument pageName is required");
		}
		
		if(!isset($input->pageContent) || $input->pageContent == ""){
			throw new InvalidInputDataException("Argument pageContent is required");
		}
		
		$sth = $this->db->prepare("insert into pages (pageName, pageContent) values(?, ?);");
		if($sth->execute(array($input->pageName, $input->pageContent)) == 0)
			throw new Exception("Database insert failed when adding page");

		return array("pageName" => $input->pageName, "pageContent" => $input->pageContent, "pageId" => $this->db->lastInsertId());
	}
	
	function listPages(){
		$retval = array();
		
		foreach($this->db->query("select * from pages", PDO::FETCH_ASSOC) as $row){
			$retval[] = $row;
		}
		
		return $retval;
	}
	
	function getPage($input, $pageName){
		$sth = $this->db->prepare("select pageName, pageContent from pages where pageName = ?");
		$sth->execute(array($pageName));
		
		$page = $sth->fetch(PDO::FETCH_ASSOC);
		if(!$page)
			throw new NoSuchResourceException();
		else 
			return $page;
	}
} 