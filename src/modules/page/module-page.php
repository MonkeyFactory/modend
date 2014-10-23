<?php

include "base/module.php";
include "pagemodel.php";
include_once "exceptions.php";

class page extends Module {
	function __construct($db){
		parent::__construct($db);
		
		$this->version = 1.1;
		$this->author = "nojan";
	}

	function RegisterRoutes($route){
		$route->register($this, "|^\/$|", array($this, "addPage"), "POST");
		$route->register($this, "|^\/(\d*)$|", array($this, "getPage"));
	}
	
	function addPage($input){
		if(!isset($input->pageTitle) || $input->pageTitle == ""){
			throw new InvalidInputDataException("argment pageTitle is required");
		}
		
		if(!isset($input->pageContent) || $input->pageContent == ""){
			throw new InvalidInputDataException("argment pageContent is required");
		}
		
		$sth = $this->db->prepare("insert into pages (pageTitle, pageContent) values(?, ?);");
		if($sth->execute(array($input->pageTitle, $input->pageContent)) == 0)
			throw new Exception("Database insert failed when adding page");

		return array("pageTitle" => $input->pageTitle, "pageContent" => $input->pageContent, "pageId" => $this->db->lastInsertId());
	}
	
	function getPage($input, $pageId){
		//$model = new PageModel($this->db);
		//$page = $model->GetPageById($pageId);

		$sth = $this->db->prepare("select pageTitle, pageContent from pages where pageId = ?");
		$sth->execute(array($pageId));
		
		$page = $sth->fetch(PDO::FETCH_ASSOC);
		if(!$page)
			throw new NoSuchResourceException();
		else 
			return $page;
	}
} 